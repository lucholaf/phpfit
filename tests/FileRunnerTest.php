<?php
error_reporting( E_ALL );

set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) . '/../' );

$baseDir = realpath( dirname( __FILE__ ) . '/..' );

require_once $baseDir . '/tools/simpletest/unit_tester.php';
require_once $baseDir . '/tools/simpletest/reporter.php';
require_once $baseDir . '/PHPFIT/FileRunner.php';


class FileRunnerTest extends UnitTestCase {
	
	public function testDoInputException() {
		$inputFilename = "noexist-input.no";
		$outputFilename = $GLOBALS['baseDir'] . "/output.html";
		
		try {
			$fr = new PHPFIT_FileRunner();
			$fr->run($inputFilename, $outputFilename);
		} catch (PHPFIT_Exception_FileIO $e) {
			$this->assertEqual( 'Input file does not exist!', $e->getMessage());
			return;
		}
		$this->fail("exptected exception not thrown");
	}
	
	public function testDoOutputException() {
		$inputFilename =  $GLOBALS['baseDir'] . "/examples/input/arithmetic.html";
		$outputFilename = "nodir/nosubdir/noexist-output.no";
		
		try {
			$fr = new PHPFIT_FileRunner();
            $fr->run($inputFilename, $outputFilename);
		} catch (PHPFIT_Exception_FileIO $e) {
			$this->assertEqual('Output file is not writable (probably a problem of file permissions)', $e->getMessage());
			return;
		}
		$this->fail("exptected exception not thrown");
	}	
}

$test = &new FileRunnerTest();
$test->run(new HtmlReporter());

?>
