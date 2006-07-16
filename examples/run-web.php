<?php

error_reporting( E_ALL | E_STRICT);

require_once 'PHPFIT.php';

if(!isset($_GET['input_filename'])) {
    die('no input file received!');
}

$output = 'output.html';

PHPFIT::run($_GET['input_filename'], $output);

echo file_get_contents( $output, true );
?>
