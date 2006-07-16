<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* PHPFit entry point to run tests
* 
* PHP version 5
*
* @category    Testing
* @package     PHPFit
* @author      Luis A. Floreani <luis.floreani@gmail.com>
* @author      gERD Schaufelberger <gerd@php-tools.net>
* @copyright   Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
* @license     LGPL http://www.gnu.org/copyleft/lesser.html
* @version     0.1.0
*/

require_once 'PHPFIT/Exception/FileIO.php';
require_once 'PHPFIT/Fixture.php';


/**
* Run tests
*
* Run fit-tests from tables stored in HTML files.  
* FileRunner provides a simple interface to process tests from CLI or 
* "remote controlled" from anther application. 
* 
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
    * @var string
    */
    
	private $input;
    
    
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
	public function run( $in, $out, $fixturesDirectory = null) 
    {
        
        date_default_timezone_set('UTC');
        
        // check input file
        if (!PHPFIT_Fixture::fc_incpath('file_exists', $in ) || !PHPFIT_Fixture::fc_incpath('is_readable', $in ) || !$in) {
            throw new PHPFIT_Exception_FileIO( 'Input file does not exist!', $in );
        }
        
        // check output file
        if(!is_writable(realpath($out)) || !$out ) {
            throw new PHPFIT_Exception_FileIO( 'Output file is not writable (probably a problem of file permissions)', realpath($out) );
        }
        
        // summary data
        $this->fixture->summary['input file']   = $in;
        $this->fixture->summary['output file']  = $out;
        $this->fixture->summary['input update'] = date( 'F d Y H:i:s.', filemtime( $in ) );
        
        // load input data
        $this->input = file_get_contents($in, true);
        
        $this->process($fixturesDirectory);
		
        // save output
        file_put_contents($out, $this->fixture->toString());
        
        return true;
	}
    
	public function process($fixturesDirectory) 
    {        
        $this->fixture  = new PHPFIT_Fixture($fixturesDirectory);
        $this->fixture->doInput($this->input);        
	}

}
?>