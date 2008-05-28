<?php

class PHPFIT_FixtureLoader {
    
    private static $fitPrefix = 'fit.';
    private static $fitFixturesDirectory = 'PHPFIT/Fixture/';
    
    /**
    * @param string $fixtureName
    * @param string $fixturesDirectory
    * @return PHPFIT_Fixture
    */
    public static function load($fixtureName, $fixturesDirectory) {
        
        $fixtureInfo = self::getFixtureInfo($fixtureName);
        
        if ($fixtureInfo['fit']) {
            $filename =  $fixtureInfo['filename'];   
        } else {
            $filename = $fixturesDirectory . $fixtureInfo['filename'];
        }
        
        if (PHPFIT_Fixture::fc_incpath('is_readable', $filename)) {
            require_once $filename;
        } else {
            throw new Exception( 'Could not load Fixture ' . $fixtureInfo['classname'] . ' from ' . $filename);
        }
        return new $fixtureInfo['classname'];
    }

    /**
    * @param string $fixtureName
    * @return array
    */    
    public static function getFixtureInfo($fixtureName) {       
        
        if( strncmp( self::$fitPrefix, $fixtureName, strlen(self::$fitPrefix) ) == 0 ) {            
            $filenamePiece = self::getFitFilenamePiece($fixtureName);
            $classname = self::getFitClassname($filenamePiece);
            $array['fit'] = true;
        } else {
            $filenamePiece = self::getCommonFilenamePiece($fixtureName);
            $classname = self::getCommonClassname($filenamePiece);           
            $array['fit'] = false;
        }
        
        $array['filename'] = $filenamePiece . '.php';
        $array['classname'] = $classname;
        return $array;
    }
    
    /**
    * @param string $fixtureName
    * @return string
    */      
    private static function getFitFilenamePiece($fixtureName) {
        $fixtureName = substr( $fixtureName, strlen(self::$fitPrefix));
        return self::$fitFixturesDirectory . $fixtureName;
    }
    
    /**
    * @param string $fixtureName
    * @return string
    */     
    private static function getCommonFilenamePiece($fixtureName) {
        return str_replace( '.', '/', $fixtureName );
    }

    /**
    * @param string $filenamePiece
    * @return string
    */     
    private static function getFitClassname($filenamePiece) {
        return str_replace( '/', '_', $filenamePiece );
    }

    /**
    * @param string $filenamePiece
    * @return string
    */     
    private static function getCommonClassname($filenamePiece) {
        $pos = strrpos($filenamePiece, '/');
        if ($pos !== false)
	        return substr($filenamePiece, $pos+1);
	    else
	    	return $filenamePiece;
    }
}

?>
