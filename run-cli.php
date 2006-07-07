<?php

error_reporting( E_ALL | E_STRICT );

require_once 'PHPFIT.php';

$fixturesDirectory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

if( count( $argv ) != 3 ) {
    fwrite( STDERR, "Invalid number of arguments. input file and output file expected\n" );
    return 1;
}

PHPFIT::run($argv[1], $argv[2], $fixturesDirectory);

?>
