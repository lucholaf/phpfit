<?php

class PHPFIT_TypeAdapter {
    
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
    * Create adapter for fixture's method
    * 
    * @param PHPFIT_Fixture $fixture
    * @param string $name
    */
	public static function onMethod(PHPFIT_Fixture $fixture, $name ) {
        $type               = $fixture->getType( $name, true );
        $adapter            = self::on( $fixture, $type );
        $adapter->method    = $name;
        return $adapter;
	}
    
    /**
    * Create adapter for fixture's field
    * 
    * @param PHPFIT_Fixture $fixture
    * @param string $name
    */
	public static function onField(PHPFIT_Fixture $fixture, $name ) {
        $type           = $fixture->getType( $name );
        $adapter        = self::on( $fixture, $type );
        $adapter->field = $name;
        return $adapter;       
    }
    
    /**
    * Create generic adapter for fixture
    * 
    * @param PHPFIT_Fixture $fixture
    * @param string $type of variables
    */
	public static function on(PHPFIT_Fixture $fixture, $type ) {
		$adapter          = self::adapterFor( $type );
		$adapter->init( $fixture, $type );
		$adapter->target  = $fixture;
		return $adapter;
	}
    
    /**
    * auxiliary function to include requested adapter
    * 
    * @param string $type
    * @return PHPFIT_TypeAdapter
    */   
    private static function loadAdapter( $name ) {
        // already loaded
        if( class_exists( 'PHPFIT_TypeAdapter_' . $name ) ) {
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
    public static function adapterFor( $type ) {
        if( self::is_bool( $type ) ) {
            self::loadAdapter( 'Boolean' );
            return new PHPFIT_TypeAdapter_Boolean();
        }
        
        if( self::is_int( $type ) ) {
            self::loadAdapter( 'Integer' );
            return new PHPFIT_TypeAdapter_Integer();
        }
        
        if( self::is_double( $type ) ) {
            self::loadAdapter( 'Double' );
            return new PHPFIT_TypeAdapter_Double();
        }
        
        if( self::is_string( $type ) ) {
            self::loadAdapter( 'String' );
            return new PHPFIT_TypeAdapter_String();
        }
        if ($type == "ScientificDouble") {
            self::loadAdapter( 'ScientificDouble' );
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
	public static function is_bool( $type ) {
        if( $type == 'boolean' || $type == 'bool' ) {
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
	public static function is_int($type) {
        if( $type == 'integer' || $type == 'int' ) {
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
	public static function is_double( $type ) {
		return $type == 'double';
	}
    
    /**
    * check for adapter type: string
    * 
    * @param string $type
    * @return true if type matches, false otherwise
    */
	public static function is_string($type) {
		return $type == 'string';
	}
    

    /**
    * @param PHPFIT_Fixture $fixture
    * @param string $type
    */
	public function init( $fixture, $type ) {
		$this->fixture = $fixture;
		$this->type = $type;
	}
    
    /**
    * @param mixed $value
    */
	public function set( $value ) {
        // suggested by Julian Harris
        $object = $this->target;
        $field = $this->field;
        $object->{$field} = $value;
	}
    
    /**
    * @return mixed
    */
	public function get() {
		if ($this->field != null) {
			return $this->field->get($this->target);
		}
        
		if ($this->method != null) {
			return $this->invoke();
		}
	}

    /**
    * @return mixed return value of a method
    */    
	public function invoke() {
        $method = $this->method;
        return $this->target->$method();
	}

    /**
    * @param mixed $o
    * @return string
    */    
	public function toString( $o ) {
		if( $o == null ) {
			return 'null';
        }
        
		if( is_object( $o ) ) {
			return $o->toString();
        }         
        
		return strval( $o );
	}
}
?>