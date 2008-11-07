<?php

require_once 'PHPFIT/Fixture.php';
require_once 'PHPFIT/Exception/LoadFixture.php';

class PHPFIT_FixtureLoader
{

    private static $fitPrefix = 'fit.';
    private static $fitFixturesDirectory = 'PHPFIT/Fixture';

	private static $fixturesDirectory = '';

	/**
	 * Set the default fixtures directory for loading user fixtures
	 * 
	 * @param string $fixturesDirectory
	 */
	public static function setFixturesDirectory($fixturesDirectory)
	{
	    if (empty($fixturesDirectory)) {
	        self::$fixturesDirectory = '';
	    } else {
	    	self::$fixturesDirectory = rtrim($fixturesDirectory, '/\\') . '/';
	    }
	}

	/**
	 * Add a directory to the path
	 */
	public static function addFixturesDirectory($fixturesDirectory)
	{
	    if (empty($fixturesDirectory)) {
	        return;
	    }
    	self::$fixturesDirectory .= PATH_SEPARATOR . rtrim($fixturesDirectory, '/\\') . '/';
	}

    /**
    * @param string $fixtureName
    * @param string $fixturesDirectory
    * @return PHPFIT_Fixture
    */
    public static function load($fixtureName, $fixturesDirectory = '')
    {
        if (substr($fixtureName, 0, strlen(self::$fitPrefix)) == self::$fitPrefix) {
            return self::loadFitFixture($fixtureName); // if $fixtureName starts with "fit."
        } else {
            return self::loadUserFixture($fixtureName, $fixturesDirectory);
        }
    }

    /**
    * Example: $fixtureName = My.Path.To.Fixture
    * Will search for the file My/Path/to/Fixture.php
    * Then, first will try to find a class named "Fixture"
    * Otherwise, will try to find a class named "My_Path_To_Fixture" (pear-style)
    *
    * @param string $fixtureName
    * @param string $fixturesDirectory
    */
    private static function loadUserFixture($fixtureName, $fixturesDirectory)
    {
        $filename = str_replace('.', '/', $fixtureName) . '.php';

		self::loadFixtureFile($filename, $fixturesDirectory);
    
        $pos = strrpos($fixtureName, '.');
        if ($pos !== false) {
            $commonClassname =  substr($fixtureName, $pos + 1);
        } else {
            $commonClassname = $filename;
        }
            
        if (class_exists($commonClassname)) {
            return new $commonClassname($fixturesDirectory);
        }
            
        $pearClassname =  str_replace('.', '_', $fixtureName);
        
        if (class_exists($pearClassname)) {
            return new $pearClassname($fixturesDirectory);
        }
            
        throw new Exception('Class "'. $commonClassname. '" or "' . $pearClassname . 
			'" could not be found in file ' . $filename);
    }
    
    /**
    * Example: $fixtureName = fit.Action and $fitFixturesDirectory = PHPFIT/Fixture
    * Will search in PHPFIT/Fixture/Action.php
    * Then, will try to find a class named PHPFIT_Fixture_Action
    *
    * @param string $fixtureName
    */
    private static function loadFitFixture($fixtureName)
    {
        $fixtureWithoutPrefix = str_replace('fit.', '', $fixtureName);
        
        $filename = self::$fitFixturesDirectory . '/' . str_replace('.', '/', $fixtureWithoutPrefix) . '.php';
    
        self::loadFile($filename);
        
        $classname = str_replace('/', '_', self::$fitFixturesDirectory) . '_' . $fixtureWithoutPrefix;
        
        if (class_exists($classname)) {
            return new $classname;
        }
        
        throw new Exception('Class "' . $classname . '" could not be found in file ' . $filename);
    }

	/**
	 * @param string $filename
	 * @param string $fixturesDirectory
	 * @return void
	 * @throws Exception
	 */
	protected static function loadFixtureFile($filename, $fixturesDirectory)
	{
		foreach (self::getFixturesDirectories($fixturesDirectory) as $dir) {
		    if (self::canLoadFile($dir . $filename)) {
		        self::loadFile($dir . $filename);
		        return;
		    }
		}
		throw new PHPFIT_Exception_LoadFixture($filename);
	}
    
	/**
	 * @param string $filename
	 * @return void
	 * @throws Exception
	 */
    protected static function loadFile($filename)
    {
        if (!self::canLoadFile($filename)) {
			throw new PHPFIT_Exception_LoadFixture($filename);
        }
        require_once $filename;
    }

	/**
	 * @param string $filename
	 * @return boolean
	 */
	protected static function canLoadFile($filename)
	{
	    return PHPFIT_Fixture::fc_incpath('is_readable', $filename);
	}

	/**
	 * Get an array of fixturesDirectories with trailing slash or empty string.
	 * 
	 * If the parameter $fixturesDirectory is not null, return it (beautified) and cast to array
	 * Otherwise return exploded version of self::$fixturesDirectory.
	 * 
	 * @param string $fixturesDirectory
	 * @return string
	 */
	protected static function getFixturesDirectories($fixturesDirectory = null)
	{
	    if (empty($fixturesDirectory)) {
	        return explode(PATH_SEPARATOR, self::$fixturesDirectory);
	    }
	    return (array) (rtrim($fixturesDirectory, '/\\') . '/');
	}

}
