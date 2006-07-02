<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Run tests from the CLI
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

error_reporting( E_ALL );

require_once 'PHPFIT/FileRunner.php';

// where are my fixtures?
//define( 'PHPFIT_FIXTURE_DIR', dirname( __FILE__ ) );

// this is a PEAR style package
set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) );

PHPFIT_FileRunner::main( $_SERVER['argv'] );

?>
