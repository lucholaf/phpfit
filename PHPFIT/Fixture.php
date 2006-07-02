<?php
/**
 * FIT Fixture
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
 * path to user's fixtures
 */
if( !defined( 'PHPFIT_FIXTURE_DIR' ) ) {
    define( 'PHPFIT_FIXTURE_DIR', '.' );
}

/**
 * load counter
 */
include_once 'PHPFIT/Counts.php';

/**
 * load scienttific double class
 */
include_once 'PHPFIT/ScientificDouble.php';

/**
 * load timer class
 */
include_once 'PHPFIT/RunTime.php';

/**
 * FIT Fixture
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage Fixture
 */
class PHPFIT_Fixture {

   /**
    * make the include folder available for user's fixtures
    * @var array
    */
    protected $backgroundColor  =   array(
                                    'passed'    => '#cfffcf',
                                    'failed'    => '#ffcfcf',
                                    'ignored'   => '#efefef',
                                    'error'     => '#ffffcf',
                                    );

   /**
    * collecting information of this fixture
    * @var array
    */
	public $summary = array();
	
   /**
    * count what?
    * @var object
    */	 
	public $counts;
	
   /**
    * construtor
    * 
    * instanciate counter
    */   
    function __construct() {
        $this->counts = new PHPFIT_Counts();
    }

   /**
    * Traverse all tables
    * 
    * Tables are packed in Parse-objects
    * 
    * @param Parse $tables
    */   
    public function doTables( $tables ) {

        $this->summary['run date'] = date( 'F d Y H:i:s.' );
        $this->summary['run elapsed time'] = new PHPFIT_RunTime();   

        // no tables left
        if( $tables == null ) {
            return;
        }
        
        // iterate through all tables
        while( $tables != null ) {
            $fixtureName = $this->fixtureName( $tables );
            
            if( $fixtureName == null ) {
                $tables = $tables->more;
                continue;
            }
            
            try {
                $fixture = $this->getLinkedFixtureWithArgs( $tables );
                $fixture->doTable( $tables );
            }
            catch( Exception $e ) {
                $this->exception( $fixtureName, $e );
            }
            $tables = $tables->more;        
        }
    }

   /**
    * iterate through table 
    * 
    * @param Parse $table
    * @see doRows()
    */
	 public function doTable( $table ) 
     {
		$this->doRows( $table->parts->more );
	 }

   /**
    * iterate through rows
    * 
    * @param Parse $rows
    * @see doRow()
    */
    public function doRows( $rows ) 
    {
        while( $rows != null ) {
            $more = $rows->more;
            $this->doRow( $rows );
            $rows = $more;
		}
	 }	 

   /**
    * iterate through cells
    * 
    * @param Parse $row
    * @see doCells()
    */
    public function doRow( $row ) 
    {
        $this->doCells( $row->parts );
    }
	 
   /**
    * process cells
    *
    * Generic processing of all upcoming cells. Actually, this method
    * just iterates through them and delegates to doCell()
    *
    * This method may be overwritten by a subclass (ActionFixture)
    * 
    * @param object $cells A parse object 
    * @return void
    * @see doCell()
    */
    public function doCells( $cells ) 
    {
        while( $cells ) {
            try {
                $this->doCell( $cells );
            } 
            catch( Exception $e ) {
                $this->exception( $cells, $e );
            }
            
            $cells = $cells->more;
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
        $this->ignore( $cell );
    }

   /**
    * find the name of the fixture to be executed
    *
    * @param parse $tables
    * @return string $name of the fixure
    */
	public function fixtureName( $tables ) 
    {
		return $tables->at( 0, 0, 0 );
	}

   /**
    * get a fixture with arguments
    * 
    * @param Parse tables
    * @return object Fixture
    * @see loadFixture()
    */
	protected function getLinkedFixtureWithArgs( $tables ) 
    {
		$header           = $tables->at( 0, 0, 0 );
		$fixture          = $this->loadFixture( $header->text() );
		$fixture->counts  = $this->counts;
		$fixture->summary = $this->summary;
		return $fixture;
	}

   /**
    * load a fixture by java-stylish name (dot-sparated)
    * 
    * A fixture name might be something like: eg.net.Simulator. This will
    * load eg/net/Simulator.php and intanciates the clss eg_net_Simulator. The path name
    * is realtive to the basic fixture dir.
    *
    * It also supports loading standard fixtures. They are recognized by the prefix: "fit."
    * Those fixtures are maped to the corresponding class.
    * 
    * @param string fixtureName
    * @return object Fixture
    */
	public function loadFixture( $fixtureName ) 
    {
        // load a FIT standard fixture
        if( strncmp( 'fit.', $fixtureName, 4 ) == 0 ) {
        
            // strip leading "fit."
            $fixtureName    = substr( $fixtureName, 4 );
        
            $class  = array( 'PHPFIT', 'Fixture' );
            
            // strip Fixture from fixtureName
            array_push( $class, str_replace( 'Fixture', '', $fixtureName ) );
            /*
            $pos    = strpos( $fixtureName, 'Fixture' );
            if( $pos !== false ) {
                $fixtureName    = substr(  $fixtureName, $pos, 7 );
            }
            
            array_push( $class, $fixtureName );
            */
            $file   = implode( '/', $class );
            $class  = implode( '_', $class );
        }
        else {
            $class  = str_replace( '.', '_', $fixtureName );
            //$file   = PHPFIT_FIXTURE_DIR . '/' . str_replace( '_', '/', $class );
            $file   = str_replace( '_', '/', $class );
        }
    
        // load class
        //$file  = str_replace( './', '', $file );
        
        if( !include_once $file . '.php'  ) {
            throw new Exception( 'Could not load Fixture ' . $fixtureName . 'from ' . $file . '.php' );
        }
        // instanciate 
        $fix = new $class();
        return $fix;
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
		$text = $cell->text();
        
		if( $text == '' ) {
        
            // there is no adapter
            if( $a == null ) {
                $this->info( $cell, 'error' );
                return false;
            }
            
			try {
				$this->info( $cell, $a->toString( $a->get() ) );
			} 
            catch( Exception $e ) {
				$this->info( $cell, 'error' );
			}
            return true;                     
		} 
        
        if( $a == null ) {
			$this->ignore( $cell );
            return true;
        }
        
		if( strncmp( $text, 'error', 5 ) == 0 ) {
			try {
				$result = $a->invoke();
				$this->wrong( $cell, $a->toString() );
			}
            catch( Exception $e ) {
				$this->right( $cell );
			}         
            return true;            
		} 
        
        try {
            // the value of the attribute or the return value of the method
            $result = $a->get(); 
            
            if( $a->equals( $a->parse( $text ), $result ) ) {
                $this->right( $cell );
            } 
            else {
                $result = $this->fixBoolToString( $result );
                $this->wrong( $cell, $a->toString( $result ) );
            }
        }
        catch( Exception $e ) {
            $this->exception($cell, $e);
        }
        
        return true;
	}
	
   /**
    * convert a  boolean value to corresponding string
    * 
    * @param bool $bool a boolean value
    * @return string "true" or "false"
    */   
    public function fixBoolToString( $bool ) 
    {
        if( !is_bool( $bool ) ) { 
            return $bool;		
        }
        
        //if( $result ) {
        //    return 'true';
        //}
        
        return 'false';
    }
   
   /**
    * transform an exception to a cell error 
    * 
    * @param object $cell Parse object
    * @param object $e Exception
    * @see error()
    */
    public function exception( $cell, $e ) 
    {
        $this->error( $cell, $e->getMessage() );
    }

   /**
    * place an error text into a cell 
    * 
    * @param object $cell Parse object
    * @param string $message 
    */
	public function error( $cell, $message ) 
    {
		$cell->body   = $cell->text() . ': '. $this->escape( $message );
        $cell->addToTag( ' bgcolor=" '. $this->backgroundColor['error'] . '\"' );
		$this->counts->exceptions++;
	}

   /**
    * parse value from cell
    * 
    * @param string s
    * @param string type
    * @return mixed (object or string)
    */
	public function parse( $s, $type ) 
    {
		if( $type == 'ScientificDouble' ) {
			return PHPFIT_ScientificDouble::valueOf( $s );
		}
		return $s;
	}
	
   /**
    * Add annotation to cell: right
    * 
    * @param Parse c$ell
    * @param string type
    * @return mixed (object or string)
    */
	 public function right( $cell ) 
     {
		 $cell->addToTag( ' bgcolor="' . $this->backgroundColor['passed'] . '"' );
		 $this->counts->right++;
	 }

	 /**
	 * @param Parse cell
	 * @param string actual
	 */
	 
	 public function wrong( $cell, $actual = false ) 
     {
		 $cell->addToTag( ' bgcolor="' .  $this->backgroundColor['failed'] . '"' );
		 $cell->body  = $this->escape( $cell->text() );
		 $this->counts->wrong++;
         
		 if( $actual !== false ) {
		 	$cell->addToBody( $this->label( 'expected' ) . '<hr />' . $this->escape( $actual ) . $this->label( 'actual' ) );
         }        
	 }	 
	 
	 
	/**
	 * @param Parse cell
	 * @param string message
	 */
	 
	 public function info( $cell, $message ) 
     {
		 $str = $this->infoS( $message );
		 $cell->addToBody( $str );
	 }
	 
	 
	/**
	 * @param string message
	 * @return string
	 */
	 
	 public function infoS( $message ) {
		 return ' <span style="color:#808080;">' . $this->escape( $message ) . '</span>';
	 }
	 
	 /**
	 * @param Parse cell
	 */

	 public function ignore ($cell) {
		 $cell->addToTag( ' bgcolor="' . $this->backgroundColor['ignored'] . '"' );
		 $this->counts->ignores++;
	 }
	 
	 
	/**
	 * @param string string
	 * @return string
	 */
	 
	public function label( $string ) {
		return ' <span style="color:#c08080;font-style:italic;font-size:small;">' . $string . '</span>';
	}
		
	/**
	 * @param string string
	 * @return string
	 */
	 
	public function escape($string) {
		$string = str_replace('&', '&amp;', $string);
		$string = str_replace('<', '&lt;', $string);
		$string = str_replace('  ', ' &nbsp;', $string);
		$string = str_replace('\r\n', '<br />', $string);
		$string = str_replace('\r', '<br />', $string);
		$string = str_replace('\n', '<br />', $string);
		return $string;
	}
	
   /**
    * receive member variable's type specification
    * 
    * Use the helper property typeDict to figure out what type
    * a member variable or return value of a member function is
    * 
    * Type is one of:
    *  - integer
    *  - string
    *  - array
    *  - object
    *  - object:CLASSNAME 
    *  - callable
    * 
    * @todo As PHP does automatica type conversation, I reckon this can be spared
    * @param string $name of property or method
    * @param bool $method check for return type of method
    * @return string $type
    */       
    public function getType( $name, $method = false ) {
    
        // method 
        if( $method ) {
            if( !method_exists( $this, $name ) ) {
                throw new Exception( 'Method does not exist! ' .get_class( $this ) . '->' . $name );
                return null;
            }
            $name .= '()';
        }
        // property
        else {    
            if( !property_exists( $this, $name ) ) {
                throw new Exception( 'Property does not exist! ' .get_class( $this ) . '->' . $name );
                return null;
            }
       }
        
        if( !isset( $this->typeDict[$name] ) ) {
            throw new Exception( 'Property has no definition in $typeDict! ' . get_class( $this ) . '->' . $name );
            return null;
        }
        
        return $this->typeDict[$name];
    }
   
   /**
    * CamelCaseString auxiliary function
    * 
    * @todo This looks quite fragile - consider using preg_replace
    * @param string $string
    * @return string 
    */   
    public static function camel( $string ) 
    {
        while( ( $pos = stripos($string, ' ' ) ) !== false ) {
            $characterUpper = strtoupper( $string[$pos+1] );
            $string[$pos+1] = $characterUpper;
            $string[$pos] = "&";
        }
        
        $string = str_replace('&', '', $string);
        return $string;
    }
}
?>