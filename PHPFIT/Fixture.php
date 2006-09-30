<?php

require_once 'PHPFIT/Counts.php';
require_once 'PHPFIT/ScientificDouble.php';
require_once 'PHPFIT/RunTime.php';
require_once 'PHPFIT/Parse.php';

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
    * @var PHPFIT_Counts
    */	 
	public $counts;
    
    /**
    * @var PHPFIT_Parser
    */	    
    private $parser;
    
    /**
    * @var string
    */    
    private $fixturesDirectory = null;
	
    /**
    * construtor
    * 
    * instanciate counter
    */
    
    function __construct($fixturesDirectory = null) {
        if ($fixturesDirectory) {
            $this->fixturesDirectory = $fixturesDirectory;
        }      
        $this->counts = new PHPFIT_Counts();
    }

    /**
    * Traverse all tables
    * 
    * Tables are packed in Parse-objects
    * 
    * @param PHPFIT_Parser $tables
    */   
    public function doTables( $tables ) {
        
        if (!isset($GLOBALS['SIMPLE_SUMMARY'])) {
            $this->summary['run date'] = date( 'F d Y H:i:s.' );
            $this->summary['run elapsed time'] = new PHPFIT_RunTime();
        }
        
        // iterate through all tables
        while( $tables != null ) {
            $fixtureName = $this->fixtureName( $tables );
            
            if( $fixtureName != null ) {
                try {
                    $fixture = $this->getFixture( $tables );
                    $fixture->doTable( $tables );
                } catch( Exception $e ) {
                    $this->exception( $fixtureName, $e );
                }
            }
            $tables = $tables->more;        
        }
    }
    
    /**
    * iterate through table 
    * 
    * @param PHPFIT_Parser $table
    * @see doRows()
    */
    public function doTable( $table ) {
		$this->doRows( $table->parts->more );
    }
    
    /**
    * iterate through rows
    * 
    * @param PHPFIT_Parser $rows
    * @see doRow()
    */
    public function doRows( $rows ) {
        while( $rows != null ) {
            $this->doRow( $rows );
            $rows = $rows->more;
		}
    }	 
    
    /**
    * iterate through cells
    * 
    * @param PHPFIT_Parser $row
    * @see doCells()
    */
    public function doRow( $row ) {
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
    * @param PHPFIT_Parser $cells
    * @see doCell()
    */
    public function doCells( $cells ) {
        while( $cells != null) {
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
    * This method may be overwritten by a subclass (e.g: ColumnFixture)
    * 
    * @param PHPFIT_Parser $cell
    * @return void
    */
    public function doCell( $cell ) {
        $this->ignore( $cell );
    }
    
    /**
    * find the name of the fixture to be executed
    *
    * @param PHPFIT_Parser $tables
    * @return string $name of the fixure
    */
	public function fixtureName( $tables ) {
		return $tables->at( 0, 0, 0 );
	}
    
    /**
    * get a fixture with arguments
    * 
    * @param PHPFIT_Parser $tables
    * @return PHPFIT_Fixture
    * @see loadFixture()
    */
	protected function getFixture( $tables ) {
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
    * @param string $fixtureName
    * @return PHPFIT_Fixture
    */
	public function loadFixture( $fixtureName ) {
        require_once 'FixtureLoader.php';
        
        return PHPFIT_FixtureLoader::load($fixtureName, $this->fixturesDirectory);
        
    }
    
    /**
    * check whether the value of a cell matches
    * 
    * @param PHPFIT_Parser $cell,
    * @param PHPFIT_TypeAdapter $adapter
    */
    public function checkCell( $cell, $adapter ) {
        $text = $cell->text();
        
        if( $text == '' ) {
            try {
                $this->info( $cell, $adapter->toString() );
            } catch( Exception $e ) {
                $this->info( $cell, 'error' );
            }
        } else if( $adapter == null ) {
            $this->ignore( $cell );
        } else if( strncmp( $text, 'error', 5 ) == 0 ) {
            try {
                $result = $adapter->invoke();
                $this->wrong( $cell, $adapter->toString() );
            } catch( Exception $e ) {
                $this->right( $cell );
            }         
        } else {          
            try {
                if ($adapter->equal($text)) {
                    $this->right( $cell );
                } else {
                    $this->wrong( $cell, $adapter->toString() );
                }
            }
            catch( Exception $e ) {
                $this->exception($cell, $e);
            }
        }
    }
    
    /**
    * transform an exception to a cell error 
    * 
    * @param PHPFIT_Parser $cell
    * @param Exception $e 
    * @see error()
    */
    public function exception( $cell, $e ) {
        $this->error( $cell, $e->getMessage() );
    }
    
    /**
    * place an error text into a cell 
    * 
    * @param PHPFIT_Parser $cell
    * @param string $message 
    */
    public function error( $cell, $message ) {
        $cell->body   = $cell->text() . ': '. $this->escape( $message );
        $cell->addToTag( ' bgcolor=" '. $this->backgroundColor['error'] . '\"' );
        $this->counts->exceptions++;
    }
    
    /**
    * Add annotation to cell: right
    * 
    * @param PHPFIT_Parser $cell
    */
    public function right( $cell ) {
        $cell->addToTag( ' bgcolor="' . $this->backgroundColor['passed'] . '"' );
        $this->counts->right++;
    }
    
    /**
    * @param PHPFIT_Parser $cell
    * @param string $actual
    */  
    public function wrong( $cell, $actual = false ) {
        $cell->addToTag( ' bgcolor="' .  $this->backgroundColor['failed'] . '"' );
        $cell->body  = $this->escape( $cell->text() );
        $this->counts->wrong++;
        
        if( $actual !== false ) {
            $cell->addToBody( $this->label( 'expected' ) . '<hr />' . $this->escape( $actual ) . $this->label( 'actual' ) );
        }        
    }	 
    
    
    /**
    * @param PHPFIT_Parser $cell
    * @param string $message
    */
    public function info( $cell, $message ) 
    {
        $str = $this->infoInColor( $message );
        $cell->addToBody( $str );
    }
    
    
    /**
    * @param string $message
    * @return string
    */   
    public function infoInColor( $message ) {
        return ' <span style="color:#808080;">' . $this->escape( $message ) . '</span>';
    }
    
    /**
    * @param PHPFIT_Parser $cell
    */
    public function ignore ($cell) {
        $cell->addToTag( ' bgcolor="' . $this->backgroundColor['ignored'] . '"' );
        $this->counts->ignores++;
    }
    
    
    /**
    * @param string $string
    * @return string
    */
    public function label( $string ) {
        return ' <span style="color:#c08080;font-style:italic;font-size:small;">' . $string . '</span>';
    }
    
    /**
    * @param string $string
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
    * @param bool $property: 'method' or 'field'
    * @return string
    */       
    public function getType( $object, $name, $property) {
         
        if( $property == 'method' ) {
            if( !method_exists( $object, $name ) ) {
                throw new Exception( 'Method does not exist! ' .get_class( $object ) . '->' . $name );
                return null;
            }
            $name .= '()';
        } else if ($property == 'field'){    
            if( !property_exists( $object, $name ) ) {
                throw new Exception( 'Property does not exist! ' .get_class( $object ) . '->' . $name );
                return null;
            }
        } else {
            throw new Exception( 'getType(): No property Method or Field defined! ');
        }
        
        if( !isset( $object->typeDict[$name] ) ) {
            throw new Exception( 'Property has no definition in $typeDict! ' . get_class( $object ) . '->' . $name );
            return null;
        }
        
        return $object->typeDict[$name];
    }
    
    /**
    * CamelCaseString auxiliary function
    * 
    * @todo This looks quite fragile - consider using preg_replace
    * @param string $string
    * @return string
    */   
    public static function camel( $string ) {
        while( ( $pos = stripos($string, ' ' ) ) !== false ) {
            $characterUpper = strtoupper( $string[$pos+1] );
            $string[$pos+1] = $characterUpper;
            $string[$pos] = "&";
        }
        
        $string = str_replace('&', '', $string);
        return $string;
    }
    
    /**
    * @param function $function
    * @param string $file
    * @return return mixed 
    */    
    public static function fc_incpath($function, $file) {
        $paths = explode(PATH_SEPARATOR, get_include_path() . PATH_SEPARATOR);
        
        foreach ($paths as $path) {
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;
            if ($function($fullpath)) {
                return $fullpath;
            }
        }
        return false;
    }    
}
?>