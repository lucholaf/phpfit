<?php

require_once 'Dialog.php';
require_once 'PHPFIT/Fixture/Action.php';

class Simulator {

    public static $system;
    public static $time;
    
    public static $nextSearchComplete = 0;
    public static $nextPlayStarted = 0;
    public static $nextPlayComplete = 0;

    public static function schedule($seconds){
        return self::$time + self::round($seconds);
    }

    public static function sooner ($soon, $event) {
        return ($event > self::$time && $event < $soon) ? $event : $soon;
    }
    
    public static function nextEvent($bound) {
        $result = $bound;
        $result = self::sooner($result, self::$nextSearchComplete);
        $result = self::sooner($result, self::$nextPlayStarted);
        $result = self::sooner($result, self::$nextPlayComplete);
        return $result;
    }
    
    public static function perform() {
        if (self::$time == self::$nextSearchComplete)     {MusicLibrary::searchComplete();}
        if (self::$time == self::$nextPlayStarted)        {MusicPlayer::playStarted();}
        if (self::$time == self::$nextPlayComplete)       {MusicPlayer::playComplete();}
    }
    
    public static function advance($future) {
        while (self::$time < $future) {
            self::$time = self::nextEvent($future);
            self::perform();
        }
    }
    
    public static function delay($seconds) {
        self::advance(self::schedule(self::round($seconds)));
    }

    public function waitSearchComplete() {
        self::advance(self::$nextSearchComplete);
    }

    public function waitPlayStarted() {
        self::advance(self::$nextPlayStarted);
    }

    public function waitPlayComplete() {
        self::advance(self::$nextPlayComplete);
    }
    
    private static function round($seconds) {
        return $seconds;
        //return intval($seconds + 0.5);
    }
    
    public static function failLoadJam() {
        PHPFIT_Fixture_Action::$actor = new Dialog("load jamed", PHPFIT_Fixture_Action::$actor);
    }
    
}

Simulator::$system = new Simulator();
Simulator::$time = time();

?>