<?php

require_once 'PHPFIT/Fixture.php';

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

?>

