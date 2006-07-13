<?php
error_reporting( E_ALL );

set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) . '/../' );

$baseDir = realpath( dirname( __FILE__ ) . '/..' );

require_once $baseDir . '/tools/simpletest/unit_tester.php';
require_once $baseDir . '/tools/simpletest/reporter.php';
require_once $baseDir . '/PHPFIT/Fixture.php';

class FixtureTest extends UnitTestCase {
	
	public function testCamelOneSpace() {
		$string = "my string";
		$this->assertEqual("myString", PHPFIT_Fixture::camel($string));
	}
	
	public function testCamelTwoSpace() {
		$string = "my string two";
		$this->assertEqual("myStringTwo", PHPFIT_Fixture::camel($string));
	}
}

$test = &new FixtureTest();
$test->run(new HtmlReporter());

?>
