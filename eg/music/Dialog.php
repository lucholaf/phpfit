<?php

require_once 'PHPFIT/Fixture.php';
require_once 'eg/music/MusicPlayer.php';

class Dialog extends PHPFIT_Fixture {
    private $theMessage;
    private $caller;
    
    public function __construct($message, $caller) {
        $this->theMessage = $message;
        $this->caller = $caller;
    }
        
    public function message() {
        return $this->theMessage;
    }
    
    public function ok() {
        if ($this->theMessage == "load jamed") {
            MusicPlayer::stop();
        }
        PHPFIT_Fixture_Action::$actor = $this->caller;
    }
    
	public $typeDict = array(
    "message()" => "string"
    );   
}

?>