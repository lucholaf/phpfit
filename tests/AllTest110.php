<?php
//AllTest.php does not work on PHP 5.3. This works with SimpleTest 1.1.0 

//the following path should point to the code of SimpleTest 1.1.0 
$simpleTestFolderPath = '../../simpletest/'; //warning, removing this line may cause remote inclusion exploit !!!

require_once $simpleTestFolderPath. 'unit_tester.php'; 
require_once $simpleTestFolderPath. 'reporter.php'; 

$test = new TestSuite('FIT tests');

$test->addFile('tests/ParseTest.php'); 
$test->addFile('tests/ExampleTest.php');
$test->addFile('tests/FixtureTest.php');
$test->addFile('tests/ClassHelperTest.php');
$test->addFile('tests/FixtureLoaderTest.php');
$test->addFile('tests/FileRunnerTest.php');
$test->addFile('tests/FrameworkTest.php');

if (TextReporter::inCli()) {
    $success = $test->run(new TextReporter());
} else {
    $success = $test->run(new HtmlReporter());
}
if (!$success) {
    // Exit with error code to make the ant build fail.
    exit(1);
}
?>