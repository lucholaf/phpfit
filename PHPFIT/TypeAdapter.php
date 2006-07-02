<?php
/**
 * FIT TypeAdapter
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @subpackage FileRunner
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */


/**
 * FIT TypeAdapter
 *
 * The type adapter makes it possible to "cast" from HTML input into PHP variables.
 * Even if PHP is sort of type-free and does automatic casts, this class makes it
 * possible to check and validate variables including their types.
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage FileRunner
 */
class PHPFIT_TypeAdapter 
{
   /**
    * target fixture object
    * @var object
    */
    public $target;
    
   /**
    * fixture object
    * @var object
    */
	public $fixture;
    
   /**
    * named field
    * @var string
    */
	public $field;
	
   /**
    * named method
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
    * @param Fixture target
    * @param string variable name
    */
	public static function onMethod( $fixture, $name ) 
    {
        $type               = $fixture->getType( $name, true );
        $adapter            = self::on( $fixture, $type );
        $adapter->method    = $name;
        return $adapter;
	}

   /**
    * Create adapter for fixture's field
    * 
    * @param Fixture target
    * @param string variable name
    */
	public static function onField( $fixture, $name ) 
    {
        $type           = $fixture->getType( $name );
        $adapter        = self::on( $fixture, $type );
        $adapter->field = $name;
        return $adapter;       
    }

   /**
    * Create generic adapter for fixture
    * 
    * @param Fixture target
    * @param string type of variables
    */
	public static function on( $fixture, $type ) {
		$adapter          = self::adapterFor( $type );
		$adapter->init( $fixture, $type );
		$adapter->target  = $fixture;
		return $adapter;
	}

   /**
    * auxiliary function to include requested adapter
    * 
    * @param string $type
    * @return object an instance of PHPFIT_TypeAdapter
    */   
    private static function loadAdapter( $name ) 
    {
        // already loaded
        if( class_exists( 'PHPFIT_TypeAdapter_' . $name ) ) {
            return true;
        }
        return include_once 'PHPFIT/TypeAdapter/' . $name . '.php';
    }

   /**
    * load actual adaptor for a specified type
    * 
    * @param string $type
    * @return object an instance of PHPFIT_TypeAdapter
    */   
    public static function adapterFor( $type ) 
    {
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
        
        return new PHPFIT_TypeAdapter();
    }        

   /**
    * check for adapter type: boolean
    * 
    * @param string $type
    * @return true if type matches, false otherwise
    */
	public static function is_bool( $type ) 
    {
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
	public static function is_double( $type ) 
    {
		return $type == 'double';
	}
    
   /**
    * check for adapter type: string
    * 
    * @param string $type
    * @return true if type matches, false otherwise
    */
	public static function is_string($type) 
    {
		return $type == 'string';
	}

	public function init( $fixture, $type ) 
    {
		$this->fixture = $fixture;
		$this->type = $type;
	}

	public function set( $value ) 
    {
		$r = new ReflectionClass($this->target);
		$prop = $r->getProperty($this->field);
		$prop->setValue($this->target, $value);
	}

	public function get() {
		if ($this->field != null) {
			if ($this->field instanceof self)
				return $this->field->get($this->target);
			return "";
		}

		if ($this->method != null) {
			$sal = $this->invoke();
			return $sal;
		}
	}

	public function invoke() {
		$r = new ReflectionClass($this->target);
		$method = $r->getMethod($this->method);
		return $method->invoke($this->target);
	}

	/**
	 * @param string s
	 * @return Object
	 */

	/* it is run just when TypeAdapter is not a subclass */
	public function parse($s) {
		return $this->fixture->parse($s, $this->type);
	}

	/**
	 * @return boolean
	 */

	public function equals( $a, $b ) {
		if ($a instanceof PHPFIT_ScientificDouble )
			return $this->scientificEquals( $a, $b );
	}

	public static function scientificEquals( $a, $b ) {
		return $a->equals( $b->toString() );
	}

	public function toString( $o ) 
    {
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