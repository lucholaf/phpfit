<?php

require_once 'PHPFIT/ClassHelper.php';
require_once 'PHPFIT/FixtureLoader.php';

class ClassHelperTest extends UnitTestCase {

    protected $classes = array(
        'ArithmeticColumnFixture',
        'ArithmeticColumnFixtureWithStaticTypeDict'
    );
    
    protected $objects = array();

    /**
     * Lazy object instantiation
     */
    protected function getInstance($class)
    {
        if (empty($this->objects[$class])) {
            PHPFIT_FixtureLoader::load('eg.' . $class, dirname(dirname(__FILE__)));
            $this->objects[$class] = new $class;
        }
        return $this->objects[$class];
    }

    public function testGetInstance()
    {
        foreach ($this->classes as $key => $class) {
            $object = $this->getInstance($class);
            $this->assertIsA($object, $class, 'Self test of getInstance');
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
        foreach ($this->classes as $class) {
            $object = $this->getInstance($class);
            $this->assertEqual(PHPFIT_ClassHelper::getType($object, 'floating', 'method'), 'double', 'getType for method given object' . $class);
            $this->assertEqual(PHPFIT_ClassHelper::getType($object, 'x', 'field'), 'integer', 'getType for property given object' . $class);
        }
    }

    public function testGetTypeForMethodOrField()
    {
        foreach ($this->classes as $class) {
            $object = $this->getInstance($class);
            $this->assertEqual(PHPFIT_ClassHelper::getTypeForMethodOrField(array($object, 'floating', 'method')), 'double', 'getType for method given object' . $class);
            $this->assertEqual(PHPFIT_ClassHelper::getTypeForMethodOrField(array($object, 'x', 'field')), 'integer', 'getType for property given object' . $class);
        }
    }
}

?>