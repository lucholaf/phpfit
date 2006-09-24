<?php

require_once 'PHPFIT/TypeAdapter.php';

class PHPFIT_Fixture_Action extends PHPFIT_Fixture {
    
    /**
    * dictionary of variable types
    * @var int 
    * @see getType()
    */
    protected $typeDict = array();
    
    /**
    * @var PHPFIT_Parse
    */
    protected $cells;
    
    /**
    * actor that has been startet
    * @var PHPFIT_Fixture
    * @see start()
    */
	private static $actor = null;
	
    /**
    * Implements start fixture 
    * 
    * Start aClass - Subsequent commands are directed to an instance of 
    * aClass. This is similar to navigating to a particular GUI screen.
    * 
    */       
    public function start() {
        $aClass         = $this->cells->more->text();
        self::$actor    = $this->loadFixture( $aClass );
    }
    
    /**
    * Implements enter fixture 
    * 
    * Enter aMethod anArgument - Invoke aMethod with anArgument (of type 
    * determined by aMethod.) This is similar to entering values into GUI fields.
    * 
    */       
    public function enter() {
        $aMethod    = $this->camel( $this->cells->more->text() );
        $anArgument = $this->cells->more->more->text();
        self::$actor->$aMethod( $anArgument );
    }
    
    /**
    * Implements press fixture 
    * 
    * Press aMethod - Invoke aMethod with no arguments. This is 
    * similar to pressing a GUI button.
    * 
    */       
    public function press() {
        $aMethod    = $this->camel( $this->cells->more->text() );
        self::$actor->$aMethod();
    }
    
    /**
    * Implements check fixture 
    * 
    * Check aMethod aValue - Invoke aMethod with no arguments. 
    * Compare the returned value with aValue. This is similar to reading 
    * values from a GUI screen.
    * 
    */       
    public function check() {
        
        $aMethod    = $this->camel( $this->cells->more->text() );
        $aValue     = $this->cells->more->more;
        
        //echo "<br>$aMethod";
        $adapter    = PHPFIT_TypeAdapter::on( self::$actor, $aMethod, self::$actor, 'method');  
        $this->checkCell( $aValue, $adapter );
        
    }
    
    /**
    * process cells
    *
    * In case of ActionFixture each row has to be interpreted as a command
    * Processing cells means to execute one command. 
    *
    * An action fixture interprets tables for which the first column contains 
    * one of a small number of commands. Subsequent columns contain values 
    * interpreted by the particular command. The generic action fixture offers 
    * only four commands, but subclasses may extend this set.
    * 
    * @param PHPFIT_Parse $cells
    */
	public function doCells( $cells ) {
        
		try {
            $this->cells = $cells;
            
            $method = $cells->text(); 
            if( !method_exists( $this, $method ) ) {
                throw new Exception( 'Action fixture cannot call the request command. Method ' 
                . get_class( $this ) . '->' . $method . ' does not exist' );
            }
            $this->$method();      
		} 
        catch( Exception $e ) {
            $this->exception( $cells, $e );
        }
	}
}
?>