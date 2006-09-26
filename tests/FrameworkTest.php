<?php

require_once 'PHPFIT/Parse.php';
require_once 'PHPFIT/ScientificDouble.php';
require_once 'PHPFIT/Fixture/Column.php';

class FrameworkTest extends UnitTestCase {
    
	public function testRuns() {
		$this->doRun("arithmetic", 38, 9, 0, 2);
		$this->doRun("compensation", 24, 0, 0, 0);
		$this->doRun("CalculatorExample", 75, 9, 0, 0);
		$this->doRun("MusicExample", 95, 0, 0, 0);
	}
	
	public function doRun($file, $right, $wrong, $ignores, $exceptions) {
		$input = 'examples/input/' . $file . '.html';
		$tables = PHPFIT_Parse::create(file_get_contents($input, true));
		$fixture = new PHPFIT_Fixture();
		$fixture->doTables($tables);
        
        //echo $tables->toString();
        
		$this->assertEqual($right, $fixture->counts->right);
		$this->assertEqual($wrong, $fixture->counts->wrong);
		$this->assertEqual($ignores, $fixture->counts->ignores);
		$this->assertEqual($exceptions, $fixture->counts->exceptions);
	}
	
	public function testTypeAdapter() {
        $f = new TestFixture();
        
        $a = PHPFIT_TypeAdapter::on($f, "sampleInt", $f, 'field');
        $a->set($a->parse("123456"));
        $this->assertTrue(is_int($a->parse("123456")));
        $this->assertEqual(123456, $f->sampleInt);
        $this->assertEqual("-234567", strval($a->parse("-234567")));
        
        $a = PHPFIT_TypeAdapter::on($f, "sampleFloat", $f, 'field');
        $a->set($a->parse("2.34"));
        $this->assertTrue(is_float($a->parse("2.34")));
        $this->assertTrue(abs(2.34 - $f->sampleFloat) < 0.00001);
        
        
        $a = PHPFIT_TypeAdapter::on($f, "pi", $f, 'method');
        $this->assertTrue(is_float($a->invoke()));
        $this->assertTrue(abs(3.14159 - $a->invoke()) < 0.00001);
        
        $a = PHPFIT_TypeAdapter::on($f, "name", $f, 'field');
        $a->set($a->parse("xyzzy"));
        $this->assertTrue(is_string($a->parse("xyzzy")));
        $this->assertEqual("xyzzy", $f->name);
        
        $a = PHPFIT_TypeAdapter::on($f, "sampleBoolean", $f, 'field');
        $a->set($a->parse("true"));
        $this->assertTrue(is_bool($a->parse("true")));
        $this->assertEqual(true, $f->name);
        
        // TODO: Arrays and Dates
        
	}
	
	public function testEscape() {
		$junk = "!@#$%^*()_-+={}|[]\\:\";',./?`";
		$this->assertEqual($junk, PHPFIT_Fixture::escape($junk));
		$this->assertEqual("", PHPFIT_Fixture::escape(""));
		$this->assertEqual("&lt;", PHPFIT_Fixture::escape("<"));
		$this->assertEqual("&lt;&lt;", PHPFIT_Fixture::escape("<<"));
        $this->assertEqual("x&lt;", PHPFIT_Fixture::escape("x<"));
		$this->assertEqual("&amp;", PHPFIT_Fixture::escape("&"));
		$this->assertEqual("&lt;&amp;&lt;", PHPFIT_Fixture::escape("<&<"));
		$this->assertEqual("&amp;&lt;&amp;", PHPFIT_Fixture::escape("&<&"));
		$this->assertEqual('a &lt; b &amp;&amp; c &lt; d', PHPFIT_Fixture::escape('a < b && c < d'));
		$this->assertEqual('a<br />b', PHPFIT_Fixture::escape('a\nb'));
	}	
	
	public function testScientificDouble() {
		$pi = 3.141592865;
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("3.14")->equals($pi));
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("3.142")->equals($pi));
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("3.1416")->equals($pi));
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("3.14159")->equals($pi));
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("3.141592865")->equals($pi));
        
		$this->assertFalse(PHPFIT_ScientificDouble::valueOf("3.140")->equals($pi));
		$this->assertFalse(PHPFIT_ScientificDouble::valueOf("3.144")->equals($pi));
		$this->assertFalse(PHPFIT_ScientificDouble::valueOf("3.1414")->equals($pi));
		$this->assertFalse(PHPFIT_ScientificDouble::valueOf("3.141592863")->equals($pi));
        
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("6.02e23")->equals(6.02e23));
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("6.02E23")->equals(6.024E23));
		$this->assertTrue(PHPFIT_ScientificDouble::valueOf("6.02e23")->equals(6.016e23));
		$this->assertFalse(PHPFIT_ScientificDouble::valueOf("6.02e23")->equals(6.026e23));
		$this->assertFalse(PHPFIT_ScientificDouble::valueOf("6.02e23")->equals(6.014e23));
        
        
	}
}

class TestFixture extends PHPFIT_Fixture_Column { // used in testTypeAdapter
	public $sampleBoolean = true;
	public $sampleInt = 1;
	public $sampleFloat = 1.2;
	public function pi() {return 3.14159862;}
	public $name = "bla"; // string
	public $sampleArray; // int[]
	public $sampleDate; // Date
	
	public $typeDict = array(
    "sampleBoolean" => "boolean",
    "sampleInt" => "integer",
    "sampleFloat" => "double",
    "pi()" => "double",
    "name" => "string"
	);	
}

?>

