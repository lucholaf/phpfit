<?php

require_once 'PHPFIT.php';

$GLOBALS['SIMPLE_SUMMARY'] = 1;

class ExampleTest extends UnitTestCase {
	public $mustFilename;
	public $isFilename;
	public $runFilename;
	
	public function setUp() {
		$this->isFilename = "examples/output.html";
	}
	
	public function tearDown() {
        
        PHPFIT::run($this->runFilename, $this->isFilename);
		
		$must = file_get_contents($this->mustFilename, true);
		$is = file_get_contents($this->isFilename, true);

		$this->assertEqual($is, $must);
	}
	
	public function testArithmeticExample() {
		$this->mustFilename = "examples/output/arithmetic.html";
		$this->runFilename = "examples/input/arithmetic.html";
	}
	
	public function testCompensationExample() {
		$this->mustFilename = "examples/output/compensation.html";
		$this->runFilename = "examples/input/compensation.html";
	}
    
    public function testCalculatorExample() {
		$this->mustFilename = "examples/output/CalculatorExample.html";
		$this->runFilename = "examples/input/CalculatorExample.html";
	}

}

//unset($GLOBALS['SIMPLE_SUMMARY']);

?>

