<?php

require_once 'PHPFIT/FileRunner.php';

class PHPFIT {
    
    /**
    * @param string $inputFilename
    * @param string $outputFilename
    * @param string $fixturesDirectory
    */
    public static function run($inputFilename, $outputFilename, $fixturesDirectory = null) {
        $fr = new PHPFIT_FileRunner();
        try {
            return $fr->run($inputFilename, $outputFilename, $fixturesDirectory);
        } catch( PHPFIT_Exception_FileIO $e ) {
            die( $e->getMessage() . " : " . $e->getFilename() );
        } catch( PHPFIT_Exception_Parse $e ) {
            die( $e->getMessage() . " at offset " . $e->getOffset());
        } catch( Exception $e ) {
            die( 'Caught unknown exception: ' . $e->getMessage() );
        }
    }
}

?>
