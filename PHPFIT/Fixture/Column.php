<?php
/**
 * FIT Fixture ColumnFixture
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
 * load class Fixture
 */ 
include_once 'PHPFIT/Fixture.php';

/**
 * FIT Fixture: ColumnFixture
 *
 * A ColumnFixture maps columns in the test data to fields or methods of its 
 * subclasses. SimpleExample and CalculatorExample use column fixtures.
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage Fixture
 */
class PHPFIT_Fixture_Column extends PHPFIT_Fixture {

   /**
    * TypeAdapter
    * @var object
    */
	protected $columnBindings;
    
   /**
    * Excecution state
    * @var bool
    */
	protected $hasExecuted = false;
	
   /**
    * Process a table's row
    * 
    * @param Parce rows
    */	
    public function doRows( $rows ) 
    {
        $this->bind( $rows->parts );
        parent::doRows( $rows->more );
    }
	
   /**
    * Process a table's row
    * 
    * @param Parce rows
    */  
    public function doRow( $row ) 
    {
        $this->hasExecuted = false;
        try {
            $this->reset();
            parent::doRow( $row );
            if( !$this->hasExecuted ) {
                $this->execute();
            }
        } 
        catch( Exception $e ) {
            $this->exception($row->leaf(), $e);
        }
    }
	
   /**
    * process a single cell
    *
    * Generic processing of a table cell. Well, this function 
    * just ignores cells. 
    * 
    * This method may be overwritten by a subclass (ColumnFixture)
    * 
    * @param object $cell A parse object 
    * @return void
    */
    public function doCell( $cell ) 
    {
        $adapter    = null;
        if( isset( $this->columnBindings[$cell->count] ) ) {
            $adapter    = $this->columnBindings[$cell->count];
        }
        
        try {
            $text   = $cell->text();
            
            if( $text === '' ) {
                $this->checkCell( $cell, $adapter );
                return;
            }
            
            // skip the rest if there is no adapter
            if( $adapter == null ) {
                $this->ignore( $cell );
                return;
            }
            
            // a column can be a value
            if( $adapter->field != null ) {
                $adapter->set( $adapter->parse( $text ) );
                return;
            }
            
            // or a column can be method
            if( $adapter->method != null ) {
                $this->checkCell( $cell, $adapter );
                return;
            }
        }
        catch( Exception $e ) {
            $this->exception( $cell, $e );
        }
    
    }

   /**
    * check whether the value of a cell matches
    * 
    * @param Parse $cell,
    * @param TypeAdapter $a
    * @return bool true on success, false otherwise
    */
    public function checkCell( $cell, $a ) 
    {
        if( !$this->hasExecuted ) {
            try {
                $this->execute();
            } 
            catch(Exception $e) {
                $this->exception ($cell, $e);
            }
            $this->hasExecuted = true;
        }
        
        parent::checkCell( $cell, $a ); 
    }
	 
	 
   /**
    * bind columns of table header to functions and properties
    * 
    * @param Parse $head 
    */
	protected function bind( $heads ) 
    { 
		$this->columnBindings = array( $heads->size() );
        
		for( $i=0; $heads != null; $heads = $heads->more ) {
			//echo "<br>".$heads->text();
			$name = $heads->text();
			$suffix = "()";
			try {
				if ($name == "") {
					$this->columnBindings[$i] = null;
				} else if (strstr($name, $suffix) !== false) {
					$this->columnBindings[$i] = $this->bindMethod(substr($name, 0, strlen($name)-strlen($suffix)));
				} else {
					$this->columnBindings[$i] = $this->bindField($name);
				}
			} catch (Exception $e) {
				$this->exception($heads, $e);
			}
			$i=$i+1;
		}
	}
	
	
	/**
	 * @param String name
	 * @return TypeAdapter
	 */
	 
	protected function bindMethod($name) {
		return PHPFIT_TypeAdapter::onMethod($this, $name);
	}

	/**
	 * @param String name
	 * @return TypeAdapter
	 */
	 
	protected function bindField($name) {
		return PHPFIT_TypeAdapter::onField($this, $name);
	}
	
	public function reset() {
	
	}
	
	public function execute() {
	
	}

}
?>