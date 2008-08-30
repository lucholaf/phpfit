<?php

require_once 'PHPFIT/FixtureLoader.php';

class FixtureLoaderTest extends UnitTestCase {

    public function testFitFixtures() {
        $fixtureInfo = PHPFIT_FixtureLoader::getFixtureInfo('fit.Action');

        $this->assertEqual('PHPFIT/Fixture/Action.php', $fixtureInfo['filename']);
        $this->assertEqual('PHPFIT_Fixture_Action', $fixtureInfo['classname']);
    }

    public function testCommonFixtures() {
        $fixtureInfo = PHPFIT_FixtureLoader::getFixtureInfo('eg.Arith');

        $this->assertEqual('eg/Arith.php', $fixtureInfo['filename']);
        $this->assertEqual('Arith', $fixtureInfo['classname']);
    }
}

?>