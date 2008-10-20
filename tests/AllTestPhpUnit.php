<?php
error_reporting(E_ALL | E_STRICT);
set_include_path(dirname(dirname(__FILE__)) . PATH_SEPARATOR . get_include_path());
require_once 'tests/UnitTestCase.php';

class AllTestPhpUnit
{
	protected static $testDir = 'tests';
	
	protected static $tests = array(
		'ClassHelperTest',
		'ExampleTest',
		'FileRunnerTest',
		'FixtureLoaderTest',
		'FixtureTest',
		'FrameworkTest',
		'ParseTest'
	);

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('FIT tests');
        foreach (self::getTestClasses() as $class) {
            self::addTestClass($suite, $class);
        }
        return $suite;
    }

	protected static function getTestClasses()
	{
	    return self::$tests;
	}

	protected static function addTestClass($suite, $class)
	{
	    self::loadTestClass($class);
	    $suite->addTestSuite($class);
	}	

	protected static function loadTestClass($class)
	{
	    require_once self::$testDir . '/' . $class . '.php';
	}
}
