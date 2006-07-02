#!/usr/bin/php
<?php
/**
 * run tests
 * This script is meant to be called from CLI
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
error_reporting( E_ALL | E_STRICT );

// where are my fixtures?
define( 'PHPFIT_FIXTURE_DIR', dirname( __FILE__ ) );

// this is a PEAR style package
set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) );

include 'PHPFIT/FileRunner.php';
PHPFIT_FileRunner::main( $_SERVER['argv'] );
?>