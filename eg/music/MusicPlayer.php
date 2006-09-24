<?php

require_once 'eg/music/MusicPlayer.php';
require_once 'eg/music/Simulator.php';

class MusicPlayer {
    
    public static $paused = 0;
    public static $playing = null;
    
    public static function play($music) {
        if (self::$paused == 0) {
            music::$status = "loading";
            $seconds = ($music == self::$playing) ? 0.3 : 2.5 ;
            Simulator::$nextPlayStarted = Simulator::schedule($seconds);
        } else {
            music::$status = "playing";
            Simulator::$nextPlayStarted = Simulator::schedule(self::$paused);
            self::$paused = 0;
        } 
    }
    
    public static function pause() {
        Music::$status = "pause";
        
        if (self::$playing != null && self::$paused == 0) {
            self::$paused = Simulator::$nextPlayComplete - Simulator::$time;
            Simulator::$nextPlayComplete = 0;
        }
    }

    public static function playStarted() {
        Music::$status = "playing";
        self::$playing = MusicLibrary::$looking;
        Simulator::$nextPlayComplete = Simulator::schedule(self::$playing->seconds);
    }

    public static function playComplete() {
        Music::$status = "ready";
        self::$playing = null;
    }
    
    public static function minutesRemaining() {
        return 3.666;
    }
    
}

?>