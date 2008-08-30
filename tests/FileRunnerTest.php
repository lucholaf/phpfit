<?php

require_once 'PHPFIT/FileRunner.php';

class FileRunnerTest extends UnitTestCase {

    public function testDoInputException() {
        $inputFilename = "noexist-input.no";
        $outputFilename = "output.html";

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
        $inputFilename =  "examples/input/arithmetic.html";
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

?>