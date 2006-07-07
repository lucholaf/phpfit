<?php

error_reporting( E_ALL | E_STRICT);

require_once 'PHPFIT.php';

$fixturesDirectory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

$file = '';
if( isset( $_GET['file'] ) ) {
    $file = $_GET['file'];
}

$output = 'output.html';

PHPFIT::run( $file, $output, $fixturesDirectory);

echo file_get_contents( $output );
?>
