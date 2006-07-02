<?php
/**
 * FIT FileRunner
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @subpackage FileRunner
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */
 
/**
 * load exception clas: FileIO
 */
include_once 'PHPFIT/Exception/FileIO.php';

/**
 * load exception clas: Parse
 */
include_once 'PHPFIT/Exception/Parse.php';

/**
 * FIT FileRunner
 *
 * Run fit-tests from tables stored in HTML files.  
 * FileRunner provides a simple interface to process tests from CLI or 
 * "remote controlled" from anther application. 
 * 
 * @see main()
 * @see run()
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage FileRunner
 */
class PHPFIT_FileRunner {

   /**
    * running fixture object
    * @var Fixture
    */
	private $fixture;
    
   /**
    * table parse
    * @var Parse
    */
	private $tables;
    
	/**
	 * @var string
	 */

	private $input;

   /**
    * Emulate c-stylish main() function to run applicattion on command line
    *
    * The most common usage from any script.  
    *  FileRunner::main( $_SERVER['argv'] );
    * 
    * return codes:
    *  - 0 everything went alright
    *  - 1 invalid number of arguments
    *  - 2 file io problem
    *  - 3 parse exception
    *  - 127 unexpected exception
    * 
    * @param array argv
    * @return int 0 on success, or value greater 1 on error 
    * @see run()
    */
	public static function main( $argv ) {
    
       /**
        * open stderr for writing
        * required for NON CLI misuse of FileRunner::main()
        */
        if( !defined( 'STDERR' ) ) {
            throw new FileIOException( 'STDERR is not defined - use PHP-CLI to call this method' );
        }
    
        if( count( $argv ) != 3 ) {
            fwrite( STDERR, "Invalid number of arguments. input file and output file expected\n" );
            return 1;
        }
        
		try {		
			$fr = new PHPFIT_FileRunner();
			$fr->run( $argv[1], $argv[2] );
		} 
        catch( PHPFIT_Exception_FileIO $e ) {
			fwrite( STDERR, $e->getMessage() . ": " . $e->getFilename() . "\n" );
            return 2;
		} 
        catch( PHPFIT_Exception_Parse $e ) {
            fwrite( STDERR, $e->getMessage() . " @ " . $e->getErrorOffset() . "\n" );
            return 3;
        } 
        catch( Exception $e ) {
			fwrite( STDERR, 'Caught unknown exception: ' . $e->getMessage() . "\n" );
            return 127;
		}
        
        return 0;
	}


   /**
    * run test 
    * 
    * Process all tables in input file and store result in output file.
    *
    * Example:
    * <pre>
    *  $fr = new FileRunner();
    *  $fr->run( 'infilt.html', 'outfile.html' );
    * </pre>
    * 
    * @param string $in path to input file
    * @param string $out path to output file
    * @return bool always true
    */
	public function run( $in, $out ) {
    
        // check input file
        if( !file_exists( $in ) ) {
            throw new PHPFIT_Exception_FileIO( 'Input file does not exist!', $in );
        }
        if( !is_readable( $in ) ) {
            throw new PHPFIT_Exception_FileIO( 'Input file does not exist!', $in );
        }
        
        // check output file
        if( file_exists( $out ) ) {
            if( !is_writable( $out ) ) {
                throw new PHPFIT_Exception_FileIO( 'Output file is not writable (probably a problem of file permissions)', $in );
            }
        }
        else {
            if( !is_writable( dirname( $out ) ) ) {
                throw new PHPFIT_Exception_FileIO( 'Cannot create output file in given folder. (probably a problem of file permissions)', $in );
            }
        }
        
        // summary data
        $this->fixture->summary['input file']   = $in;
        $this->fixture->summary['output file']  = $out;
        $this->fixture->summary['input update'] = date( 'F d Y H:i:s.', filemtime( $in ) );
        
        // load input
        $this->input = file_get_contents( $in );
        
        // run tests
		$this->process();
		
        // save output
        file_put_contents( $out, $this->tables->toString() );
        
        return true;
	}

   /**
    * process tables
    * 
    * @return bool always true
    */
	public function process() 
    {
        include_once 'PHPFIT/Fixture.php';
        include_once 'PHPFIT/Parse.php';
        
        $this->fixture  = new PHPFIT_Fixture();
   
        $this->tables   = new PHPFIT_Parse( $this->input );
        $this->fixture->doTables( $this->tables );
        
        return true;      
	}
}
?>