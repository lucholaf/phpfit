<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Run tests from a web browser
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

require_once 'PHPFIT/FileRunner.php';

$file = '';
if( isset( $_GET['file'] ) ) {
    $file = $_GET['file'];
}

// where are my fixtures?
//define( 'PHPFIT_FIXTURE_DIR', dirname( __FILE__ ) );

// this is a PEAR style package
set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) );

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
