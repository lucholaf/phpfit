<?php

require_once 'PHPFIT.php';

class ExampleTest extends UnitTestCase {
	public $mustFilename;
	public $isFilename;
	public $runFilename;
	
	public function setUp() {
		$this->isFilename = "examples/output.html";
		if (!@unlink($this->isFilename))
			echo "could not clean output! (permission problem?)<br>";
	}
	
	public function tearDown() {
		PHPFIT::run($this->runFilename, $this->isFilename);
		
		$must = file_get_contents($this->mustFilename, true);
		$is = file_get_contents($this->isFilename, true);

		$this->assertEqual($is, $must);
	}
	
	public function xtestArithmeticExample() {
		$this->mustFilename = "examples/output/arithmetic.html";
		$this->runFilename = "examples/input/arithmetic.html";
	}
	
	public function xtestCompensationExample() {
		$this->mustFilename = "examples/output/compensation.html";
		$this->runFilename = "examples/input/compensation.html";
	}

}

?>

