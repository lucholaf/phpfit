<?php
require_once 'PHPFIT/Exception/ClassHelper/MissingMethod.php';
require_once 'PHPFIT/Exception/ClassHelper/MissingProperty.php';
require_once 'PHPFIT/Exception/ClassHelper/MissingTypeDictEntry.php';

class PHPFIT_ClassHelper
{
	/**
	 * A map of the support types and their normalized names
	 * @var array
	 */
	protected static $types = array(
		'boolean'   => 'boolean',
		'bool'      => 'boolean',
		'integer'   => 'integer',
		'int'       => 'integer',
		'short'     => 'integer',
		'float'     => 'double',
		'double'    => 'double',
		'character' => 'string',
		'string'    => 'string',
		'array'     => 'array',
		'void'      => 'void',
		'null'      => 'void',
	);

    /**
    * receive member variable's type specification
    *
    * Use the helper property typeDict to figure out what type
    * a member variable or return value of a member function is
    *
    * Type is one of:
    *  - boolean or bool
    *  - integer or int
    *  - float or double
    *  - string
    *  - array
    *  - void or null
    *  - object:CLASSNAME or just CLASSNAME
    *
    * @todo As PHP does automatica type conversation, I reckon this can be spared
    * @param string|object $classOrObject object to retrieve return type from
    * @param string $name of property or method
    * @param string $property: 'method' or 'field'
    * @return string
    */
    public static function getType($classOrObject, $name, $property, $allowPrivateAccess = false)
    {
        if ($property == 'method') {
			return self::getTypeForMethod($classOrObject, $name);
        } elseif ($property == 'field') {
			return self::getTypeForField($classOrObject, $name, $allowPrivateAccess);
        } else {
            throw new PHPFIT_Exception_ClassHelper('getType(): No property Method or Field defined!');
        }
    }

	/**
	 * @param string $type
	 * @return string
	 */
	public static function getNormalizedType($type)
	{
	    $checktype = strtolower($type);
	    if (self::isBaseType($checktype)) {
	        return self::$types[$checktype];
	    }
	    return $type;
	}

	/**
	 * @param string
	 * @return string
	 */
	public static function isBaseType($type)
	{
	    if (!is_string($type)) {
	        throw new PHPFIT_Exception_ClassHelper('Checking type for non-string');
	    }
	    return array_key_exists($type, self::$types);
	}

	/**
	 * Delegates to getType(), but the argument here is an array
	 * <code>
	 * 	$methodOrField = array(
	 * 		$classOrObject,
	 * 		$name,
	 * 		$property
	 * 	);
	 * </code>
	 * 
	 * For future use with PHPFITLibrary
	 * @param array $methodOrField
	 */
	public static function getTypeForMethodOrField($methodOrField)
	{
	    if (array_keys($methodOrField) != array(0, 1, 2)) {
	        throw new PHPFIT_Exception_ClassHelper('getTypeForMethodOrField() must' .
	        		' be called with a numerically indexed array.');
	    }
	    return self::getType($methodOrField[0], $methodOrField[1], $methodOrField[2]);
	}

	/**
     * @param string|object $classOrObject object to retrieve return type from
     * @param string $name of method
     * @return string
	 */
	public static function getTypeForMethod($classOrObject, $name)
	{
		self::checkMethodExists($classOrObject, $name);
        $name .= '()';
        return self::getNormalizedType(self::getTypeDictValue($classOrObject, $name));
	}

	/**
     * @param string|object $classOrObject object to retrieve return type from
     * @param string $name of property
     * @return string
	 */
	public static function getTypeForField($classOrObject, $name, $allowPrivateProperty = false)
	{
		self::checkPropertyExists($classOrObject, $name, $allowPrivateProperty);
        return self::getNormalizedType(self::getTypeDictValue($classOrObject, $name));
	}

	/**
	 * Get the return value type from the typeDict
	 * 
	 * The typeDict may contain entries like
	 * <code>
	 * $typeDict = array(
	 * 	'method1()' => 'integer',
	 * 	'method2()' => array('return' => 'boolean'),
	 * 	'method3()' => array('args' => array('int', 'int'), 'return' => 'string'),
	 * )
	 * </code>
	 * 
	 * @param array $array
	 * @param string $name
	 * @return string
	 */
	protected static function getTypeDictValue($classOrObject, $name)
	{
        $type = self::getTypeDictEntry($classOrObject, $name);
        if (is_array($type)) {
			$type = self::getArrayValue($type, 'return');
        }
		if (is_null($type)) {
			throw new PHPFIT_Exception_ClassHelper_MissingTypeDictEntry(self::getClassForClassOrObject($classOrObject), $name);
		}
        return $type;
	}

	/**
	 * @return array
	 */
	public static function getArgTypesForMethod($classOrObject, $name)
	{
		self::checkMethodExists($classOrObject, $name);
        $name .= '()';
        $type = self::getTypeDictEntry($classOrObject, $name);
        if (!is_array($type)) {
            return null;
        }
	    $argTypes = self::getArrayValue($type, 'args');
		if (!is_null($argTypes)) {
		    $argTypes = (array) $argTypes;
		    foreach ($argTypes as $key => $argType) {
		        $argTypes[$key] = self::getNormalizedType($argType);
		    }
		}
	    return $argTypes;
	}

	/**
	 * Get value from array or null, if the entry is not set
	 * 
	 * @param array $array
	 * @param string|int $key
	 * @return mixed
	 */
	protected static function getArrayValue(array $array, $key)
	{
	    if (!array_key_exists($key, $array)) {
	        return null;
	    }
	    return $array[$key];
	}

	/**
     * @param string|object $classOrObject
     * @param string $name
     * @return string
	 */
	protected static function getTypeDictEntry($classOrObject, $name)
	{
        $typeDict = self::getTypeDictForClassOrObject($classOrObject);
		return self::getArrayValue($typeDict, $name);
	}

    /**
     * Get the $typeDict property for the object or class.
     * 
     * If we get an object, we first try to access the normal typeDict property.
     * If the typeDict property of the object is static, or $classOrObject is
     * a class name, we access the static typeDict property of the class.
     * 
     * The static typeDict entries are recursively merged with those of
     * the parent classes.
     * 
     * @param string|object $classOrObject
     * @return array
     */
    protected static function getTypeDictForClassOrObject($classOrObject)
    {
		// End point of recursion
		if (empty($classOrObject)) {
		    return array();
		}
        if (is_object($classOrObject) && isset($classOrObject->typeDict)) {
            $typeDict = $classOrObject->typeDict;
        } else {
        	$typeDict = self::getStaticTypeDictForClass($classOrObject);
        }
        // Recursive merging of typeDict entries
        $parent = self::getParentClassForClassOrObject($classOrObject);
        return array_merge(self::getTypeDictForClassOrObject($parent), $typeDict);
    }

	/**
	 * @param string|object $classOrObject
     * @return string
	 */
	protected static function getParentClassForClassOrObject($classOrObject)
	{
	    return get_parent_class($classOrObject);
	}

	/**
     * @param string|object $classOrObject
     * @return array
     */
	protected static function getStaticTypeDictForClass($class)
	{
        $classReflector = self::getReflectionClassForClassOrObject($class);
        $staticProperties = $classReflector->getStaticProperties();
        if (empty($staticProperties['typeDict'])) {
            $typeDict = array();
        } else {
            $typeDict = $staticProperties['typeDict'];
        }
        return $typeDict;
	}

    /**
     * @param string|object $classOrObject
     * @return string
     */
    public static function getClassForClassOrObject($classOrObject)
    {
        if (is_object($classOrObject)) {
            $classOrObject = get_class($classOrObject);
        }
        if (!class_exists($classOrObject)) {
            throw new PHPFIT_Exception_ClassHelper('getClassForClassOrObject() unknown class ' . $classOrObject . '.');
        }
        return $classOrObject;
    }

    /**
     * @param string|object $classOrObject
     * @return ReflectionClass
     */
    protected static function getReflectionClassForClassOrObject($classOrObject)
    {
        $class = self::getClassForClassOrObject($classOrObject);
        return new ReflectionClass($class);
    }

	/**
	 * @return void
	 * @throws Exception
	 */
	protected static function checkMethodExists($classOrObject, $name)
	{
        if (!method_exists($classOrObject, $name)) {
			throw new PHPFIT_Exception_ClassHelper_MissingMethod(self::getClassForClassOrObject($classOrObject), $name);
        }
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	protected static function checkPropertyExists($classOrObject, $name, $allowPrivateProperty = false)
	{
		$property = null;
        if ($allowPrivateProperty) {
            $reflectionClass = self::getReflectionClassForClassOrObject($classOrObject);
            $property = $reflectionClass->getProperty($name);
        }
        if (is_null($property) && !property_exists($classOrObject, $name)) {
			throw new PHPFIT_Exception_ClassHelper_MissingProperty(self::getClassForClassOrObject($classOrObject), $name);
        }
	}	
}
