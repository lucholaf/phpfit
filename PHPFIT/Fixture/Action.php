<?php
/**
 * FIT Fixture: ActionFixture
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @subpackage Fixture
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */

/**
 * load class TypeAdapter
 */
include_once 'PHPFIT/TypeAdapter.php';

/**
 * FIT Fixture: ActionFixture
 * 
 * An action fixture interprets rows as a sequence of commands to be performed 
 * in order. It interprets tables for which the first column contains one of a
 * small number of commands. Subsequent columns contain values interpreted by 
 * the particular command. The generic action fixture offers only four commands, 
 * but subclasses may extend this set.
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage Fixture
 */
class PHPFIT_Fixture_Action extends PHPFIT_Fixture {

   /**
    * dictionary of variable types
    * @var int 
    * @see getType()
    */
    protected $typeDict = array();
  
   /**
    * @var Parse
    */
    protected $cells;

   /**
    * actor that has been startet
    * @var Fixture
    * @see start()
    */
	private $actor = null;
	
   /**
    * Implements start fixture 
    * 
    * Start aClass - Subsequent commands are directed to an instance of 
    * aClass. This is similar to navigating to a particular GUI screen.
    * 
    * @return void
    */       
    public function start() {
        $aClass         = $this->cells->more->text();
        $this->actor    = $this->loadFixture( $aClass );
    }
    
   /**
    * Implements enter fixture 
    * 
    * Enter aMethod anArgument - Invoke aMethod with anArgument (of type 
    * determined by aMethod.) This is similar to entering values into GUI fields.
    * 
    * @return void
    */       
    public function enter() {
        $aMethod    = $this->camel( $this->cells->more->text() );
        $anArgument = $this->cells->more->more->text();
        $this->actor->$aMethod( $anArgument );
    }
    
   /**
    * Implements press fixture 
    * 
    * Press aMethod - Invoke aMethod with no arguments. This is 
    * similar to pressing a GUI button.
    * 
    * @return void
    */       
    public function press() {
        $aMethod    = $this->camel( $this->cells->more->text() );
        $this->actor->$aMethod();
    }

   /**
    * Implements check fixture 
    * 
    * Check aMethod aValue - Invoke aMethod with no arguments. 
    * Compare the returned value with aValue. This is similar to reading 
    * values from a GUI screen.
    * 
    * @return void
    */       
    public function check() {
        $aMethod    = $this->camel( $this->cells->more->text() );
        $aValue     = $this->cells->more->more;
        
        $adapter    = PHPFIT_TypeAdapter::onMethod( $this->actor, $aMethod );  
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
    * @param object $cells A parse object 
    * @return void
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