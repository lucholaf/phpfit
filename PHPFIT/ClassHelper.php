<?php
class PHPFIT_ClassHelper {

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
    *  - object:CLASSNAME
    *
    * @todo As PHP does automatica type conversation, I reckon this can be spared
    * @param string|object $classOrObject object to retrieve return type from
    * @param string $name of property or method
    * @param bool $property: 'method' or 'field'
    * @return string
    */
    public static function getType( $classOrObject, $name, $property) {

        if( $property == 'method' ) {
            if( !method_exists( $classOrObject, $name ) ) {
                throw new Exception( 'Method does not exist! ' .self::getClassForClassOrObject( $classOrObject ) . '->' . $name );
                return null;
            }
            $name .= '()';
        } else if ($property == 'field'){
            if( !property_exists( $classOrObject, $name ) ) {
                throw new Exception( 'Property does not exist! ' .self::getClassForClassOrObject( $classOrObject ) . '->' . $name );
                return null;
            }
        } else {
            throw new Exception( 'getType(): No property Method or Field defined! ');
        }

        $typeDict = self::getTypeDictForClassOrObject($classOrObject);

        if( !isset( $typeDict[$name] ) ) {
            throw new Exception( 'Property has no definition in $typeDict! ' . self::getClassForClassOrObject( $classOrObject ) . '->' . $name );
            return null;
        }

        return $typeDict[$name];
    }

    /**
     * Get the $typeDict property for the object or class.
     * 
     * If we get an object, we first try to access the normal typeDict property.
     * If the typeDict property of the object is static, or $classOrObject is
     * a class name, we access the static typeDict property of the class.
     * 
     * @param string|object $classOrObject
     * @return array
     */
    protected static function getTypeDictForClassOrObject($classOrObject)
    {
        if (is_object($classOrObject)) {
            if (isset($classOrObject->typeDict)) {
                return $classOrObject->typeDict;
            }
        }
        $classReflector = self::getReflectionClassForClassOrObject($classOrObject);
        $staticProperties = $classReflector->getStaticProperties();
        if (empty($staticProperties['typeDict'])) {
            return array();
        } else {
            return $staticProperties['typeDict'];
        }
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
        assert(class_exists($classOrObject));
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

}
?>
