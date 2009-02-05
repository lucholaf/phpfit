<?php

require_once 'tools/simpletest/unit_tester.php';
require_once 'tools/simpletest/reporter.php';

$test = &new GroupTest('FIT tests');


$test->addTestFile('tests/ParseTest.php');
$test->addTestFile('tests/ExampleTest.php');
$test->addTestFile('tests/FixtureTest.php');
$test->addTestFile('tests/ClassHelperTest.php');
$test->addTestFile('tests/FixtureLoaderTest.php');
$test->addTestFile('tests/FileRunnerTest.php');
$test->addTestFile('tests/FrameworkTest.php');

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
