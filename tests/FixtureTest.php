<?php

require_once 'PHPFIT/Fixture.php';

class FixtureTest extends UnitTestCase {
	
	public function testCamelOneSpace() {
		$this->assertEqual('myString', PHPFIT_Fixture::camel('my string'));
	}
	
	public function testCamelTwoSpace() {
		$this->assertEqual('myStringTwo', PHPFIT_Fixture::camel('my string two'));
	}
}

?>

