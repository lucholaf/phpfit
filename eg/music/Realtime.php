<?php

require_once 'PHPFIT/Fixture/TimedAction.php';
require_once 'eg/music/Simulator.php';

class Realtime extends PHPFIT_Fixture_TimedAction {
    
    public $system;
    
    public function __construct() {
        $this->system = Simulator::$system;
    }
    
    public function pause() {
        $this->system->delay($this->cells->more->text());
    }
    
    public function await() {
    }
    
    public function fail() {
    }
    
}

?>
