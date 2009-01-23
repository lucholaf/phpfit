<?php

abstract class PHPFIT_TypeAdapter
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
     * @return boolean
     */
    private static function loadAdapter($name)
    {
        // already loaded
        if (class_exists(self::getAdapterClassName($name))) {
            return true;
        }
        $classFile = PHPFIT_Fixture::fc_incpath('file_exists', self::getAdapterClassFile($name));
        if (false === $classFile) {
	        return false;
        }
    	require_once self::getAdapterClassFile($name);
    	return class_exists(self::getAdapterClassName($name));
    }

	/**
	 * @param string $name
	 * @return string class name
	 */
	private static function getAdapterClassName($name)
	{
	    return 'PHPFIT_TypeAdapter_' . ucfirst($name);
	}

	/**
	 * @param string $name
	 * @return string file name with path
	 */
	private static function getAdapterClassFile($name)
	{
		return self::$fitTypeAdaptersDirectory . ucfirst($name) . '.php';
	}

    /**
     * load actual adapter for a specified type
     *
     * @param string $type
     * @return PHPFIT_TypeAdapter
     */
    public static function adapterFor($type)
    {
		$type = self::getNormalizedType($type);
		if (self::loadAdapter($type)) {
		    $className = self::getAdapterClassName($type);
		    return new $className;
		}
		throw new Exception("No type adapter available for $type");
    }

	/**
	 * @param string $type
	 * @return string
	 */
	protected static function getNormalizedType($type)
	{
		return PHPFIT_ClassHelper::getNormalizedType($type);
	}

    /**
     * check for adapter type: boolean
     *
     * @param string $type
     * @return true if type matches, false otherwise
     */
    public static function is_bool($type)
    {
		return 'boolean' == self::getNormalizedType($type);
    }

    /**
     * check for adapter type: integer
     *
     * @param string $type
     * @return true if type matches, false otherwise
     */
    public static function is_int($type)
    {
		return 'integer' == self::getNormalizedType($type);
    }

    /**
     * check for adapter type: double
     *
     * @param string $type
     * @return true if type matches, false otherwise
     */
    public static function is_double($type)
    {
		return 'double' == self::getNormalizedType($type);
    }

    /**
     * check for adapter type: string
     *
     * @param string $type
     * @return true if type matches, false otherwise
     */
    public static function is_string($type)
    {
		return 'string' == self::getNormalizedType($type);
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
     * @return string
     */
    public function toString()
    {
        $o = $this->get();
		return $this->valueToString($o);
    }

    /**
     * @param mixed $value
     * @return string
     */
	public function valueToString($value)
	{
        if ($value == null) {
            return 'null';
        }

        if (is_object($value)) {
            return $value->toString();
        }

        return strval($value);
	}

    /**
     * @param string $text
     * @return true if same, false otherwise
     */
    public function equal($text)
    {
        return $this->valueEquals($this->get(), $text);
    }
    
    /**
     * @param mixed $value
     * @param string $text
     * @return true if same, false otherwise
     */
    public function valueEquals($value, $text)
    {
        return $this->equals($this->parse($text), $value);
    }
}

