<?php

require_once 'PHPFIT/Parse.php';
require_once 'PHPFIT/Fixture.php';
require_once 'PHPFIT/FileRunner.php';


class ExampleTest extends UnitTestCase {
	public $mustFilename;
	public $isFilename;
	public $runFilename;
	
	public function setUp() {
		$this->isFilename = PHPFIT_DIR . "output.html";
		if (!@unlink($this->isFilename))
			echo "could not clean output! (permission problem?)<br>";
	}
	
	public function tearDown() {
		$args[]=3;
		$args[]=$this->runFilename;
		$args[]=$this->isFilename;
		
		FileRunner::main($args);
		
		$must = file_get_contents($this->mustFilename, true);
		$is = file_get_contents($this->isFilename, true);

		$this->assertEqual($is, $must);
	}
	
	public function xtestArithmeticExample() {
		$this->mustFilename = PHPFIT_DIR . EXAMPLES_DIR . "output/arithmetic.html";
		$this->runFilename = PHPFIT_DIR . EXAMPLES_DIR . "input/arithmetic.html";
	}
	
	public function xtestCompensationExample() {
		$this->mustFilename = PHPFIT_DIR . EXAMPLES_DIR . "output/compensation.html";
		$this->runFilename = PHPFIT_DIR . EXAMPLES_DIR . "input/compensation.html";
	}

}

?>
