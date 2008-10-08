<?php

require_once 'PHPFIT/Counts.php';
require_once 'PHPFIT/ScientificDouble.php';
require_once 'PHPFIT/RunTime.php';
require_once 'PHPFIT/Parse.php';
require_once 'PHPFIT/ClassHelper.php';
require_once 'PHPFIT/NullFixtureListener.php';

class PHPFIT_Fixture
{

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
	 * array of strings
	 * @var array
	 */
	protected $args = array();

	/**
	 * map of symbols
	 * @var array
	 */
	protected static $symbols;

	/**
	 * @var boolean
	 */
	private static $forcedAbort = false;

    /**
    * construtor
    *
    * instanciate counter
    */

    function __construct($fixturesDirectory = null)
    {
	    $this->listener = new PHPFIT_NullFixtureListener();
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
	 * Altered by Rick to dispatch on the first Fixture
	 *
	 * @param PHPFIT_Parse $tables
	 * @return void
	 */
    public function doTables($tables)
    {
        if (!isset($GLOBALS['SIMPLE_SUMMARY'])) {
            $this->summary['run date'] = date('F d Y H:i:s.');
            $this->summary['run elapsed time'] = new PHPFIT_RunTime();
        }

        // iterate through all tables
        while ($tables != null) {
            $fixtureName = $this->fixtureName($tables);
            if ($fixtureName != null) {
                try {
                    $fixture = $this->getLinkedFixtureWithArgs($tables);
                    $fixture->listener = $this->listener;
                    $fixture->interpretTables($tables);
                } catch (Exception $e) {
                    $this->exception($fixtureName, $e);
                    $this->interpretFollowingTables($tables);
                }
            }
            // $tables == null may happen now, because
            // interpretFollowingTables() advances $tables
            if ($tables != null) {
            	$tables = $tables->more;
            }
        }
        $this->listener->tablesFinished($this->counts);
        self::clearSymbols();
        //SemaphoreFixture::clearSemaphores(); //Semaphores:  clear all at end
    }

    /**
    * iterate through table
    *
    * @param PHPFIT_Parser $table
    * @see doRows()
    */
    public function doTable($table)
    {
        $this->doRows($table->parts->more);
    }

    /**
    * iterate through rows
    *
    * @param PHPFIT_Parser $rows
    * @see doRow()
    */
    public function doRows($rows)
    {
        while ($rows != null) {
            $this->doRow($rows);
            $rows = $rows->more;
        }
    }

    /**
    * iterate through cells
    *
    * @param PHPFIT_Parser $row
    * @see doCells()
    */
    public function doRow($row)
    {
        $this->doCells($row->parts);
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
    public function doCells($cells)
    {
        while ($cells != null) {
            try {
                $this->doCell($cells);
            } catch(Exception $e) {
                $this->exception($cells, $e);
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
    public function doCell($cell)
    {
        $this->ignore($cell);
    }

    /**
     * find the name of the fixture to be executed
     *
     * @param PHPFIT_Parser $tables
     * @return string $name of the fixure
     */
    public function fixtureName($tables)
    {
        return $tables->at(0, 0, 0);
    }

    /**
     * get a fixture with arguments
     *
     * @param PHPFIT_Parser $tables
     * @return PHPFIT_Fixture
     * @see loadFixture()
     */
    protected function getFixture($tables)
    {
        $header           = $tables->at(0, 0, 0);
        $fixture          = $this->loadFixture($header->text());
        $fixture->counts  = $this->counts;
        $fixture->summary = $this->summary;
        return $fixture;
    }

    /**
     * load a fixture by java-stylish name (dot-sparated)
     *
     * @param string $fixtureName
     * @return PHPFIT_Fixture
     */
    public function loadFixture($fixtureName)
    {
        require_once 'FixtureLoader.php';

        return PHPFIT_FixtureLoader::load($fixtureName, $this->fixturesDirectory);
    }

	/**
	 * Added by Rick to allow a dispatch into DoFixture
	 * 
	 * In PHP we need explicit call by reference,
	 * because $table = $table->more must propagate back to
	 * doTables().
	 * 
	 * @param PHPFIT_Parse $tables
	 * @return void
	 */
    protected function interpretTables(&$tables)
    {
        try {
        	// Don't create the first fixture again, because creation may do something important.
            $this->getArgsForTable($tables); // get them again for the new fixture object
            $this->doTable($tables);
        } catch (Exception $ex) {
            $this->exception($tables->at(0, 0, 0), $ex);
            $this->listener->tableFinished($tables);
            return;
        }
        $this->interpretFollowingTables($tables);
    }

	/**
	 * Added by Rick
	 * 
	 * In PHP we need explicit call by reference,
	 * because $table = $table->more must propagate back to
	 * doTables().
	 *
	 * @param PHPFIT_Parse $tables
	 * @return void
	 */
    private function interpretFollowingTables(&$tables)
    {
        $this->listener->tableFinished($tables);
        $tables = $tables->more;
        while ($tables != null) {
            $fixtureName = $this->fixtureName($tables);
			if (self::$forcedAbort) {
			    $this->ignore($fixtureName); //Semaphores: ignore on failed lock
			} elseif ($fixtureName != null) {
                try {
                    $fixture = $this->getLinkedFixtureWithArgs($tables);
                    $fixture->doTable($tables);
                } catch (Exception $e) {
                    $this->exception($fixtureName, $e);
                }
            }
            $this->listener->tableFinished($tables);
            $tables = $tables->more;
        }
    }

    /**
     * Added by Rick
     *
     * @param PHPFIT_Parse $tables
     * @return PHPFIT_Fixture
     * @throws Throwable
     */
    protected function getLinkedFixtureWithArgs($tables)
    {
        $header = $tables->at(0, 0, 0);
        $fixture = $this->loadFixture($header->text());
        $fixture->counts = $this->counts;
        $fixture->summary = $this->summary;
        $fixture->getArgsForTable($tables);
        return $fixture;
    }

	/**
     * @param PHPFIT_Parse $tables
     * @return void
	 */
    public function getArgsForTable($table)
    {
        $argumentList = array();
        $parameters = $table->parts->parts->more;
        while ($parameters != null) {
            $argumentList[] = $parameters->text();
            $parameters = $parameters->more;
        }
        $this->args = $argumentList;
    }

	public function getArgs()
	{
	    return $this->args;
	}

    /**
     * check whether the value of a cell matches
     *
     * @param PHPFIT_Parse $cell,
     * @param PHPFIT_TypeAdapter $adapter
     */
    public function checkCell($cell, $adapter)
    {
        $text = $cell->text();

        if ($text == '') {
            try {
                $this->info($cell, $adapter->toString());
            } catch(Exception $e) {
                $this->info($cell, 'error');
            }
        } else if ($adapter == null) {
            $this->ignore($cell);
        } else if (strncmp($text, 'error', 5) == 0) {
            try {
                $result = $adapter->invoke();
                $this->wrong($cell, $adapter->toString());
            } catch (Exception $e) {
                $this->right($cell);
            }
        } else {
            try {
                if ($adapter->equal($text)) {
                    $this->right($cell);
                } else {
                    $this->wrong($cell, $adapter->toString());
                }
            } catch(Exception $e) {
                $this->exception($cell, $e);
            }
        }
    }

    /**
     * transform an exception to a cell error
     *
     * @param PHPFIT_Parse $cell
     * @param Exception $e
     * @see error()
     */
    public function exception($cell, $e)
    {
        $this->error($cell, $e->getMessage());
    }

    /**
     * place an error text into a cell
     *
     * @param PHPFIT_Parse $cell
     * @param string $message
     */
    public function error($cell, $message)
    {
        $cell->body   = $cell->text() . ': '. $this->escape($message);
        $cell->addToTag(' bgcolor=" '. $this->backgroundColor['error'] . '\"');
        $this->counts->exceptions++;
    }

    /**
     * Add annotation to cell: right
     *
     * @param PHPFIT_Parse $cell
     */
    public function right($cell)
    {
        $cell->addToTag(' bgcolor="' . $this->backgroundColor['passed'] . '"');
        $this->counts->right++;
    }

    /**
     * @param PHPFIT_Parse $cell
     * @param string $actual
     */
    public function wrong($cell, $actual = false)
    {
        $cell->addToTag(' bgcolor="' .  $this->backgroundColor['failed'] . '"');
        $cell->body  = $this->escape($cell->text());
        $this->counts->wrong++;

        if ($actual !== false) {
            $cell->addToBody($this->label('expected') . '<hr />' . $this->escape($actual) . $this->label('actual'));
        }
    }


    /**
     * @param PHPFIT_Parse $cell
     * @param string $message
     */
    public function info($cell, $message)
    {
        $str = $this->infoInColor($message);
        $cell->addToBody($str);
    }


    /**
     * @param string $message
     * @return string
     */
    public function infoInColor($message)
    {
        return ' <span style="color:#808080;">' . $this->escape($message) . '</span>';
    }

    /**
     * @param PHPFIT_Parse $cell
     */
    public function ignore ($cell)
    {
        $cell->addToTag(' bgcolor="' . $this->backgroundColor['ignored'] . '"');
        $this->counts->ignores++;
    }


    /**
     * @param string $string
     * @return string
     */
    public static function label($string)
    {
        return ' <span style="color:#c08080;font-style:italic;font-size:small;">' . $string . '</span>';
    }

    /**
     * @param string $string
     * @return string
     */
    public static function escape($string)
    {
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
     *  - boolean or bool
     *  - integer or int
     *  - float or double
     *  - string
     *  - array
     *  - object:CLASSNAME
     *
     * @todo As PHP does automatica type conversation, I reckon this can be spared
     * @param string|object $classOrObject object to retrieve return type from
     * @param string $name of property or method
     * @param string $property: 'method' or 'field'
     * @return string
     */
    public static function getType($classOrObject, $name, $property)
	{
        return PHPFIT_ClassHelper::getType($classOrObject, $name, $property);
    }


    /**
     * CamelCaseString auxiliary function
     *
     * @param string $string
     * @return string
     */
    public static function camel($string)
    {
      	/* clear extra spaces */
        $string = preg_replace('/\s\s+/', ' ', $string);

        while (($pos = stripos($string, ' ')) !== false) {
            $string[$pos+1] = strtoupper($string[$pos+1]);
            $firstPart = substr($string, 0, $pos);
            $secondPart = substr($string, $pos + 1);
            $string = $firstPart . $secondPart;
        }

        return $string;
    }

    /**
     * @param function $function
     * @param string $file
     * @return return mixed
     */
    public static function fc_incpath($function, $file)
    {
        if ($function($file))
        return $file;

        $paths = explode(PATH_SEPARATOR, get_include_path() . PATH_SEPARATOR);

        foreach ($paths as $path) {
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;
            if ($function($fullpath)) {
                return $fullpath;
            }
        }
        return false;
    }

    /**
     * @param boolean $state
     * @return void
     */
    public static function setForcedAbort($state)
    {
		self::$forcedAbort = $state; //Semaphores
    }

    /**
     * @return void
     */
    public static function clearSymbols()
    {
        self::$symbols = array();
    }

    /**
     * @param string $name
     * @param Object $value
     * @return void
     */
    public static function setSymbol($name, $value)
    {
        self::$symbols[$name] = ($value == null)? "null" : $value;
    }

    /**
     * @param string $name
     * @return Object
     */
    public static function getSymbol($name)
    {
        if (!self::hasSymbol($name)) {
            return null;
        }
        return self::$symbols[$name];
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function hasSymbol($name)
    {
        return array_key_exists($name, self::$symbols);
    }

}
