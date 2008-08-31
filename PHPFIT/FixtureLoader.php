<?php

require_once 'PHPFIT/Fixture.php';

class PHPFIT_FixtureLoader {

    private static $fitPrefix = 'fit.';
    private static $fitFixturesDirectory = 'PHPFIT/Fixture';

    /**
    * @param string $fixtureName
    * @param string $fixturesDirectory
    * @return PHPFIT_Fixture
    */
    public static function load($fixtureName, $fixturesDirectory) {
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
    private static function loadUserFixture($fixtureName, $fixturesDirectory) {
        if ($fixturesDirectory != null)
            $filename = $fixturesDirectory . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $fixtureName) . '.php';
        else
            $filename = str_replace('.', DIRECTORY_SEPARATOR, $fixtureName) . '.php';

        self::loadFile($filename);
    
        $pos = strrpos($fixtureName, '.');
        if ($pos !== false)
            $commonClassname =  substr($fixtureName, $pos + 1);
        else
            $commonClassname = $filename;
            
        if (class_exists($commonClassname))
            return new $commonClassname;
            
        $pearClassname =  str_replace('.', '_', $fixtureName);
        
        if (class_exists($pearClassname))
            return new $pearClassname;
            
        throw new Exception('Class "'. $commonClassname. '" or "' . $pearClassname . '" could not be found in file ' . $filename);
    }
    
    /**
    * Example: $fixtureName = fit.Action and $fitFixturesDirectory = PHPFIT/Fixture
    * Will search in PHPFIT/Fixture/Action.php
    * Then, will try to find a class named PHPFIT_Fixture_Action
    *
    * @param string $fixtureName
    */
    private static function loadFitFixture($fixtureName) {
        $fixtureWithoutPrefix = str_replace('fit.', '', $fixtureName);
        
        $filename = self::$fitFixturesDirectory . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $fixtureWithoutPrefix) . '.php';
    
        self::loadFile($filename);
        
        $classname = str_replace(DIRECTORY_SEPARATOR, '_', self::$fitFixturesDirectory) . '_' . $fixtureWithoutPrefix;
        
        if (class_exists($classname))
            return new $classname;
        
        throw new Exception('Class "' . $classname . '" could not be found in file ' . $filename);
    }
    
    private static function loadFile($filename) {
        if (PHPFIT_Fixture::fc_incpath('is_readable', $filename)) {
            require_once $filename;
        } else {
            throw new Exception( 'Could not load file ' . $filename);
        }
    }

}

?>
