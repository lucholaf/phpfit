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
            return self::loadFitFixture($fixtureName);
        } else {
            return self::loadUserFixture($fixtureName, $fixturesDirectory);
        }
    }

    private static function loadUserFixture($fixtureName, $fixturesDirectory) {
        if ($fixturesDirectory != null)
            $filename = $fixturesDirectory . DIRECTORY_SEPARATOR . str_replace('.', '/', $fixtureName) . '.php';
        else
            $filename = str_replace('.', '/', $fixtureName) . '.php';

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
            
        throw new Exception('Class could not be found in file ' . $filename);
    }
    
    private static function loadFitFixture($fixtureName) {
        $fixtureWithoutPrefix = str_replace('fit.', '', $fixtureName);
        
        $filename = self::$fitFixturesDirectory . DIRECTORY_SEPARATOR . str_replace('.', '/', $fixtureWithoutPrefix) . '.php';
    
        self::loadFile($filename);
        
        $classname = str_replace('/', '_', self::$fitFixturesDirectory) . '_' . $fixtureWithoutPrefix;
        
        return new $classname;
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
