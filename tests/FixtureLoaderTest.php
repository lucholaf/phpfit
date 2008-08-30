<?php

require_once 'PHPFIT/FixtureLoader.php';

class FixtureLoaderTest extends UnitTestCase {

    public function testLoadFixtureCommonClassname() {
        try {
            $fixture = PHPFIT_FixtureLoader::load('fixtures.sample', dirname(__FILE__) . DIRECTORY_SEPARATOR);
            $this->assertTrue($fixture instanceof sample);
        } catch (Exception $e) {
            $this->fail("Exception not expected here: " . $e->getMessage());
        }
    }

    public function testLoadFixtureFail() {
        try {
            $fixture = PHPFIT_FixtureLoader::load('fixtures.nonexistingsample', dirname(__FILE__) . DIRECTORY_SEPARATOR);
            $this->fail("Exception expected here");
        } catch (Exception $e) {
            $this->pass("ok");
        }
    }

    public function testLoadFixtureFitClassname() {
        try {
            $fixture = PHPFIT_FixtureLoader::load('fit.Action', null);
            $this->assertTrue($fixture instanceof PHPFIT_Fixture_Action);
        } catch (Exception $e) {
            $this->fail("Exception not expected here: " . $e->getMessage());
        }
    }
    
    public function testLoadFixturePearClassname() {
        try {
            $fixture = PHPFIT_FixtureLoader::load('fixtures.sample_pear', dirname(__FILE__) . DIRECTORY_SEPARATOR);
            $this->assertTrue($fixture instanceof fixtures_sample_pear);
        } catch (Exception $e) {
            $this->fail("Exception not expected here: " . $e->getMessage());
        }
    }

    public function xtestFitFixtures() {
        $fixtureInfo = PHPFIT_FixtureLoader::getFixtureInfo('fit.Action');

        $this->assertEqual('PHPFIT/Fixture/Action.php', $fixtureInfo['filename']);
        $this->assertEqual('PHPFIT_Fixture_Action', $fixtureInfo['classname']);
    }

    public function xtestCommonFixtures() {
        $fixtureInfo = PHPFIT_FixtureLoader::getFixtureInfo('eg.Arith');

        $this->assertEqual('eg/Arith.php', $fixtureInfo['filename']);
        $this->assertEqual('Arith', $fixtureInfo['classname']);
    }
}

?>
