<?php

require_once 'PHPFIT/TypeAdapter.php';
require_once 'PHPFIT/Fixture.php';

class PHPFIT_Fixture_Column extends PHPFIT_Fixture {
    
    /**
    * @var PHPFIT_TypeAdapter
    */
	protected $columnBindings;
    
    /**
    * Excecution state
    * @var boolean
    */
	protected $hasExecuted = false;
	
    /**
    * Process a table's row
    * 
    * @param PHPFIT_Parse $rows
    */	
    public function doRows( $rows ) {
        $this->bind( $rows->parts ); // bind the first row
        parent::doRows( $rows->more ); // process the other rows
    }
	
    /**
    * Process a table's row
    * 
    * @param PHPFIT_Parse $row
    */  
    public function doRow( $row ) {
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
    * @param PHPFIT_Parse $cell
    */
    public function doCell( $cell ) {
        $adapter    = null;
        
        if( isset( $this->columnBindings[$cell->count] ) ) {
            $adapter    = $this->columnBindings[$cell->count];
        }
        
        try {
            $text   = $cell->text();
            
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
    * @param PHPFIT_Parse $cell,
    * @param PHPFIT_TypeAdapter $a
    * @return bool true on success, false otherwise
    */
    public function checkCell( $cell, $a ) {
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
    * @param PHPFIT_Parse $head 
    */
	protected function bind( $heads ) { 
		$this->columnBindings = array( $heads->size() );
        
		for( $i=0; $heads != null; $heads = $heads->more ) {
			$name = $heads->text();
			try {
				if ($name == "") {
					$this->columnBindings[$i] = null;
				} else if (($methodName = $this->getMethodName($name)) !== false) {
					$this->columnBindings[$i] = $this->bindMethod($methodName);
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
	* @param string $name
	* @return string
	*/
	protected function getMethodName($name) {
		$suffix = "()";
		
		/* e.g: calc price () -> returns calcPrice */
		if (stripos($name, ' ') !== false) {
			return substr($this->camel($name), 0, strlen($name) - 4);
		}
		
		/* e.g: calcPrice() -> returns calcPrice */
		if (strstr($name, $suffix) !== false)		
			return substr($this->camel($name), 0, strlen($name) - 2);
		
		return false;
	}
	
	/**
    * @param String $name
    * @return PHPFIT_TypeAdapter
    */
    
	protected function bindMethod($name) {
		return PHPFIT_TypeAdapter::on($this, $name, $this->getTargetClass(), 'method');
	}
    
	/**
    * @param String $name
    * @return PHPFIT_TypeAdapter
    */
    
	protected function bindField($name) {
        return PHPFIT_TypeAdapter::on($this, $name, $this->getTargetClass(), 'field');
	}
	
    protected function getTargetClass() {
        return $this;
    }
    
	public function reset() {
        
	}
	
	public function execute() {
        
	}
    
}

?>
