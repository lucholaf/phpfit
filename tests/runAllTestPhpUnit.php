<?php
require_once 'AllTestPhpUnit.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
$runner = new PHPUnit_TextUI_TestRunner;
$suite = AllTestPhpUnit::suite();
$result = $runner->doRun($suite);

// Check that we are still E_STRICT
if (error_reporting() !== (E_ALL | E_STRICT)) {
    echo "Warning: E_STRICT compliance was turned off during tests.\n";
}
