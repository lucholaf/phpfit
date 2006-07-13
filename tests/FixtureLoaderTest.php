<?php
error_reporting( E_ALL );

set_include_path( get_include_path()  . ':' . dirname( __FILE__ ) . '/../' );

$baseDir = realpath( dirname( __FILE__ ) . '/..' );

require_once $baseDir . '/tools/simpletest/unit_tester.php';
require_once $baseDir . '/tools/simpletest/reporter.php';
require_once $baseDir . '/PHPFIT/FixtureLoader.php';

class FixtureLoaderTest extends UnitTestCase {
	
	public function testFitFixtures() {        
        $fixtureInfo = FixtureLoader::getFixtureInfo('fit.Action');
        
        $this->assertEqual('PHPFIT/Fixture/Action.php', $fixtureInfo['filename']);
        $this->assertEqual('PHPFIT_Fixture_Action', $fixtureInfo['classname']);
	}
    
    public function testCommonFixtures() {
        $fixtureInfo = FixtureLoader::getFixtureInfo('eg.Arith');

        $this->assertEqual('eg/Arith.php', $fixtureInfo['filename']);        
        $this->assertEqual('Arith', $fixtureInfo['classname']);
    }
    
	
}

$test = &new FixtureLoaderTest();
$test->run(new HtmlReporter());

?>
