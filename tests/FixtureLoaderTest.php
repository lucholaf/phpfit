<?php

require_once 'PHPFIT/FixtureLoader.php';

class FixtureLoaderTest extends UnitTestCase {

	public function setUp()
	{
		// set initially empty path
		PHPFIT_FixtureLoader::setFixturesDirectory('');
	}

	public function tearDown()
	{
		// leave it clean for other tests
		PHPFIT_FixtureLoader::setFixturesDirectory('');
	}

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
            $fixture = PHPFIT_FixtureLoader::load('fixtures.sample.pear', dirname(__FILE__) . DIRECTORY_SEPARATOR);
            $this->assertTrue($fixture instanceof fixtures_sample_pear);
        } catch (Exception $e) {
            $this->fail("Exception not expected here: " . $e->getMessage());
        }
    }

    public function testLoadFixtureWithPathsSet() {
        try {
            $fixture = PHPFIT_FixtureLoader::load('SampleOnOnePath');
            $this->fail("Exception expected here");
        } catch (PHPFIT_Exception_LoadFixture $e) {
        }
        try {
            $fixture = PHPFIT_FixtureLoader::load('SampleOnAnotherPath');
            $this->fail("Exception expected here");
        } catch (PHPFIT_Exception_LoadFixture $e) {
        }
        PHPFIT_FixtureLoader::addFixturesDirectory(dirname(__FILE__) . '/fixtures/onePath');
        $fixture = PHPFIT_FixtureLoader::load('SampleOnOnePath');
        try {
            $fixture = PHPFIT_FixtureLoader::load('SampleOnAnotherPath');
            $this->fail("Exception expected here");
        } catch (PHPFIT_Exception_LoadFixture $e) {
        }
        PHPFIT_FixtureLoader::addFixturesDirectory(dirname(__FILE__) . '/fixtures/anotherPath');
        $fixture = PHPFIT_FixtureLoader::load('SampleOnOnePath');
        $fixture = PHPFIT_FixtureLoader::load('SampleOnAnotherPath');
    }
}

?>
