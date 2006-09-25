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
        $this->system("wait", $this->cells->more);
    }
    
    public function fail() {
        $this->system("fail", $this->cells->more);
    }

    public function press() {
        //$this->system->delay(1.2); // FIX THIS!
        $this->system->delay(0.9);
        parent::press();
    }

    public function enter() {
        $this->system->delay(0.8);
        parent::enter();
    }

    
    public function system($prefix, $cell) {
        $method = $this->camel($prefix . " " . $cell->text());
        try {
            $this->system->$method();
        } catch(Exception $e) {
            $this->exception($cell, $e);
        }       
    }
    
}

?>
