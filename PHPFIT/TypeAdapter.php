<?php

class PHPFIT_TypeAdapter
{

    /**
    * @var string
    */
    private static $fitTypeAdaptersDirectory = 'PHPFIT/TypeAdapter/';

    /**
    * @var PHPFIT_Fixture
    */
    public $target;

    /**
    * @var PHPFIT_Fixture
    */
    public $fixture;

    /**
    * @var string
    */
    public $field;

    /**
    * @var string
    */
    public $method;

    /**
    * adapter class
    * @var string
    */
    public $type = null;


    /**
    * Create adapter for fixture method or field
    *
    * @param PHPFIT_Fixture $fixture
    * @param string $name
    * @param object $object
    * @param string $property: 'field' or 'method'
    */
    public static function on(PHPFIT_Fixture $fixture, $name, $object, $property)
    {
        $type               = $fixture->getType($object, $name, $property);
        $adapter            = self::adapterFor($type);
        $adapter->init($fixture, $type);
        $adapter->target    = $object;
        $adapter->$property = $name;

        return $adapter;
    }

    /**
    * auxiliary function to include requested adapter
    *
    * @param string $type
    * @return PHPFIT_TypeAdapter
    */
    private static function loadAdapter($name)
    {
        // already loaded
        if (class_exists('PHPFIT_TypeAdapter_' . $name)) {
            return true;
        }
        return include_once self::$fitTypeAdaptersDirectory . $name . '.php';
    }

    /**
    * load actual adapter for a specified type
    *
    * @param string $type
    * @return PHPFIT_TypeAdapter
    */
    public static function adapterFor($type)
    {
        if (self::is_bool($type)) {
            self::loadAdapter('Boolean');
            return new PHPFIT_TypeAdapter_Boolean();
        }

        if (self::is_int($type)) {
            self::loadAdapter('Integer');
            return new PHPFIT_TypeAdapter_Integer();
        }

        if (self::is_double($type)) {
            self::loadAdapter('Double');
            return new PHPFIT_TypeAdapter_Double();
        }

        if (self::is_string($type)) {
            self::loadAdapter('String');
            return new PHPFIT_TypeAdapter_String();
        }
        if ($type == "ScientificDouble") {
            self::loadAdapter('ScientificDouble');
            return new PHPFIT_TypeAdapter_ScientificDouble();
        }

        return new PHPFIT_TypeAdapter();
    }

    /**
    * check for adapter type: boolean
    *
    * @param string $type
    * @return true if type matches, false otherwise
    */
    public static function is_bool($type)
    {
        if (strtolower($type) == 'boolean' || strtolower($type) == 'bool') {
            return true;
        }
        return false;
    }

    /**
    * check for adapter type: integer
    *
    * @param string $type
    * @return true if type matches, false otherwise
    */
    public static function is_int($type)
    {
        if (strtolower($type) == 'integer' || strtolower($type) == 'int') {
            return true;
        }
        return false;
    }

    /**
    * check for adapter type: double
    *
    * @param string $type
    * @return true if type matches, false otherwise
    */
    public static function is_double($type)
    {
        return (strtolower($type) == 'double' || strtolower($type) == 'float');
    }

    /**
    * check for adapter type: string
    *
    * @param string $type
    * @return true if type matches, false otherwise
    */
    public static function is_string($type)
    {
        return strtolower($type) == 'string';
    }


    /**
    * @param PHPFIT_Fixture $fixture
    * @param string $type
    */
    public function init($fixture, $type)
    {
        $this->fixture = $fixture;
        $this->type = $type;
    }

    /**
    * @param mixed $value
    */
    public function set($value)
    {
        // suggested by Julian Harris
        $object = $this->target;
        $field = $this->field;
        $object->$field = $value;
    }

    /**
    * @return mixed
    */
    public function get()
    {
        if ($this->field != null) {
            $field = $this->field;
            return $this->target->$field;
        }

        if ($this->method != null) {
            return $this->invoke();
        }

        return null;
    }

    /**
    * @return mixed return value of a method
    */
    public function invoke()
    {
        $method = $this->method;
        return $this->target->$method();
    }

    /**
    * @param mixed $o
    * @return string
    */
    public function toString()
    {

        $o = $this->get();

        if ($o == null) {
            return 'null';
        }

        if (is_object($o)) {
            return $o->toString();
        }

        return strval($o);
    }

    /**
    * @param string $text
    * @return true if same, false otherwise
    */
    public function equal($text)
    {
        return $this->equals($this->parse($text), $this->get());
    }
}

