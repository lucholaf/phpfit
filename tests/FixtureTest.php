<?php

require_once 'PHPFIT/Fixture.php';

class FixtureTest extends UnitTestCase {
	
	public function testCamelOneSpace() {
		$this->assertEqual('myString', PHPFIT_Fixture::camel('my string'));
	}

	public function testCamelExtraSpaces() {
		$this->assertEqual('myString', PHPFIT_Fixture::camel('my     string'));
	}
	
	public function testCamelTwoSpaces() {
		$this->assertEqual('myStringTwo', PHPFIT_Fixture::camel('my string two'));
	}
	
	public function testCamelWithSpaceAfterEachChar() {
		$this->assertEqual('MYSTRING', PHPFIT_Fixture::camel(' m y s t r i n g'));
	}	
}

?>

