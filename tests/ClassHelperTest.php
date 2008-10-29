<?php

require_once 'PHPFIT/ClassHelper.php';
require_once 'PHPFIT/FixtureLoader.php';

class ClassHelperTest extends UnitTestCase {

    protected $classes = array(
        'ArithmeticColumnFixture',
        'ArithmeticColumnFixtureWithStaticTypeDict',
        'ClassHelperTest_TypeDict',
        'ClassHelperTest_InheritedTypeDict',
    );
    
    protected $objects = array();

	protected static $types = array(
		'SomeClass' => 'SomeClass',
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
     * Lazy object instantiation
     */
    protected function getInstance($class)
    {
        if (empty($this->objects[$class])) {
            if (!class_exists($class)) {
            	PHPFIT_FixtureLoader::load('eg.' . $class, dirname(dirname(__FILE__)));
            }
            $this->objects[$class] = new $class;
        }
        return $this->objects[$class];
    }

	protected function getFixtureClasses()
	{
	    return array($this->classes[0], $this->classes[1]);
	}

    public function testGetInstance()
    {
        foreach ($this->classes as $key => $class) {
            $object = $this->getInstance($class);
            $this->assertIsA($object, $class, 'Self test of getInstance');
        }
    }

	public function testNormalizedType()
	{
		$this->assertEqual('Blub', PHPFIT_ClassHelper::getNormalizedType('Blub'));		
		foreach (self::$types as $type => $normtype) {
		    $this->assertEqual($normtype, PHPFIT_ClassHelper::getNormalizedType($type));
		}
	}

    public function testGetClassForClassOrObject()
    {
        foreach ($this->classes as $class) {
            $object = $this->getInstance($class);
            $this->assertEqual(PHPFIT_ClassHelper::getClassForClassOrObject($object), $class, 'Convert object to class name');
            $this->assertEqual(PHPFIT_ClassHelper::getClassForClassOrObject($class), $class, 'Return class name for given class name');
        }
    }

    public function testGetTypeForClass()
    {
        // This can only work for the static typeDict!!!
        $class = $this->classes[1];
        $this->assertEqual(PHPFIT_ClassHelper::getType($class, 'floating', 'method'), 'double', 'getType for method given classname ' . $class);
        $this->assertEqual(PHPFIT_ClassHelper::getType($class, 'x', 'field'), 'integer', 'getType for property given classname' . $class);
    }
    
    public function testGetTypeForObject()
    {
        foreach ($this->getFixtureClasses() as $class) {
            $object = $this->getInstance($class);
            $this->assertEqual(PHPFIT_ClassHelper::getType($object, 'floating', 'method'), 'double', 'getType for method given object' . $class);
            $this->assertEqual(PHPFIT_ClassHelper::getType($object, 'x', 'field'), 'integer', 'getType for property given object' . $class);
        }
    }

    public function testGetTypeForMethodOrField()
    {
        foreach ($this->getFixtureClasses() as $class) {
            $object = $this->getInstance($class);
            $this->assertEqual(PHPFIT_ClassHelper::getTypeForMethodOrField(array($object, 'floating', 'method')), 'double', 'getType for method given object' . $class);
            $this->assertEqual(PHPFIT_ClassHelper::getTypeForMethodOrField(array($object, 'x', 'field')), 'integer', 'getType for property given object' . $class);
        }
    }

	public function testInheritedTypeDictForClass()
	{
	    $this->assertInheritedTypeDictOk($this->classes[2], $this->classes[3]);
	}

	public function testInheritedTypeDictForObject()
	{
	    $mainObject = $this->getInstance($this->classes[2]);
	    $subObject = $this->getInstance($this->classes[3]);
	    $this->assertInheritedTypeDictOk($mainObject, $subObject);
	}

	public function testGetArgTypesForClass()
	{
        $class = $this->classes[3];
        $this->assertGetArgTypesOk($class);
	}

	public function testGetArgTypesForObject()
	{
        $object = $this->getInstance($this->classes[3]);
        $this->assertGetArgTypesOk($object);
	}

	public function testMissingPropertyException()
	{
	    $class = $this->classes[3];
	    $this->assertEquals('boolean', PHPFIT_ClassHelper::getTypeForField($class, 'existingProperty'));
	    try {
	        $type = PHPFIT_ClassHelper::getTypeForField($class, 'missingProperty');
	    } catch (PHPFIT_Exception_ClassHelper_MissingProperty $e) {
	    } catch (Exception $e) {
	        $this->fail('Wrong Exception;');
	    }
	}

	public function testMissingMethodException()
	{
	    $class = $this->classes[3];
	    $this->assertEquals('boolean', PHPFIT_ClassHelper::getTypeForMethod($class, 'existingMethod'));
	    try {
	        $type = PHPFIT_ClassHelper::getTypeForMethod($class, 'missingMethod');
	    } catch (PHPFIT_Exception_ClassHelper_MissingMethod $e) {
	    } catch (Exception $e) {
	        $this->fail('Wrong Exception;');
	    }
	}

	public function testMissingTypeDictEntryException()
	{
	    $class = $this->classes[3];
	    try {
	        $type = PHPFIT_ClassHelper::getTypeForField($class, 'existingPropertyWithoutTypeDictEntry');
	    } catch (PHPFIT_Exception_ClassHelper_MissingTypeDictEntry $e) {
	    } catch (Exception $e) {
	        $this->fail('Wrong Exception;');
	    }
	    try {
	        $type = PHPFIT_ClassHelper::getTypeForMethod($class, 'existingMethodWithoutTypeDictEntry');
	    } catch (PHPFIT_Exception_ClassHelper_MissingTypeDictEntry $e) {
	    } catch (Exception $e) {
	        $this->fail('Wrong Exception;');
	    }
	}

	protected function assertGetArgTypesOk($classOrObject)
	{
        $this->assertEqual(PHPFIT_ClassHelper::getArgTypesForMethod($classOrObject, 'methodWithArgType'), array('string', 'integer'));
        $this->assertEqual(PHPFIT_ClassHelper::getTypeForMethod($classOrObject, 'methodWithArgType'), 'boolean');
        // return null, if undefined
        $this->assertEqual(PHPFIT_ClassHelper::getArgTypesForMethod($classOrObject, 'plus'), null);
        $this->assertEqual(PHPFIT_ClassHelper::getArgTypesForMethod($classOrObject, 'divide'), null);
	}

	protected function assertInheritedTypeDictOk($mainClassOrObject, $subClassOrObject)
	{
        $this->assertEqual(PHPFIT_ClassHelper::getType($mainClassOrObject, 'plus', 'method'), 'integer');
        $this->assertEqual(PHPFIT_ClassHelper::getType($mainClassOrObject, 'divide', 'method'), 'integer');
        $this->assertEqual(PHPFIT_ClassHelper::getType($subClassOrObject, 'plus', 'method'), 'integer');
        $this->assertEqual(PHPFIT_ClassHelper::getType($subClassOrObject, 'divide', 'method'), 'double');
        $this->assertEqual(PHPFIT_ClassHelper::getType($subClassOrObject, 'methodWithArgType', 'method'), 'boolean');
	}
}

class ClassHelperTest_TypeDict
{
    public static $typeDict = array(
    	'plus()' => 'integer',
    	'divide()' => 'integer',
    	'existingProperty' => 'bool',
    	'existingMethod()' => 'bool',
    );

	public $existingProperty;
	public $existingPropertyWithoutTypeDictEntry;
    
    public function plus()
    {
    }

    public function divide()
    {
    }

	public function existingMethod()
	{
	}

	public function existingMethodWithoutTypeDictEntry()
	{
	}
}

class ClassHelperTest_InheritedTypeDict extends ClassHelperTest_TypeDict
{
	// Change the type of plus and add the justTestArgType method
	public static $typeDict = array( 
	    'divide()' => array('return' => 'double'),
	    'methodWithArgType()' => array('args' => array('string', 'integer'), 'return' => 'boolean'),
    );

	/**
	 * Just a test for the enhanced typeDict providing argTypes
	 * 
	 * @param string $arg1
	 * @param integer $arg2
	 */
	public function methodWithArgType($arg1, $arg2)
	{
	    return true;
	}
}

?>