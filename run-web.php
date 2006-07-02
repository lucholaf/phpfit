<?php
/**
 * run tests
 * This script is meant to be called from a web-based interface
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @version 0.1.0
 * @subpackage FileRunner
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */

$file = '';
if( isset( $_GET['file'] ) ) {
    $file = $_GET['file'];
}

// where are my fixtures?
define( 'PHPFIT_FIXTURE_DIR', dirname( __FILE__ ) );

// this is a PEAR style package
set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) );

include 'PHPFIT/FileRunner.php';

try {
    $fr = new PHPFIT_FileRunner();
    $fr->run( $file, 'output.html' );
}
catch( FileIOException $e ) {
    die( $e->getMessage() . ": " . $e->getFilename() );
} 
catch( Exception $e ) {
    die( 'Caught unknown exception: ' . $e->getMessage() );
}

// print test-results
echo file_get_contents( 'output.html' );
?>