<?php

set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) . '/..' );

require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';

$test = &new GroupTest('FIT tests');

$test->addTestFile('tests/ParseTest.php');
$test->addTestFile('tests/ExampleTest.php');
$test->addTestFile('tests/FixtureTest.php');
$test->addTestFile('tests/FixtureLoaderTest.php');
$test->addTestFile('tests/FileRunnerTest.php');
$test->addTestFile('tests/FrameworkTest.php');

if (TextReporter::inCli()) {
    exit($test->run(new TextReporter()) ? 0 : 1);
}
$test->run(new HtmlReporter());
?>