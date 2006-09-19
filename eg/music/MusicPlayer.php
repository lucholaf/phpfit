<?php

class MusicPlayer {
    
    public static $paused = 0;
    
    public static function play($music) {
        if (self::$paused == 0) {
            music::$status = "loading";
        } else {
            music::$status = "playing";
        } 
    }
}

?>