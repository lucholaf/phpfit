<?php
error_reporting( E_ALL );

//$baseDir = realpath( dirname( __FILE__ ) . '/..' );

set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) . '/../' );

require_once 'tools/simpletest/unit_tester.php';
require_once 'tools/simpletest/reporter.php';
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
		
		$must = file_get_contents($this->mustFilename);
		$is = file_get_contents($this->isFilename);

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

$test = &new ExampleTest();
$test->run(new HtmlReporter());	


?>
