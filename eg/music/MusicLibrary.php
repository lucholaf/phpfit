<?php

require_once 'Music.php';

class MusicLibrary {
	
	/**
    * @var Music 
    */
	public static $library = array();
	public static $looking = null;
	
    
	public static function load($path) {
		$fp = fopen($path, "r", true);
		
		fgets($fp); // skip column headings
		
		while ($line = fgets($fp)) {
			self::$library[] = Music::parse($line);
		}
		fclose($fp);
	}

    public static function searchComplete() {
        Music::$status = (MusicPlayer::$playing == null) ? "ready" : "playing";
    }	
	
	/**
    * @param Music $m 
    */	
    public static function select($m) {
        self::$looking = $m;
    }
    
    
    public static function displayContents() {
        $displayed = array();
        $j=0;
        for ($i=0; $i < count(self::$library); $i++) {
            if (self::$library[$i]->selected) {
                $displayed[$j++] = self::$library[$i];
            }
        }
        return $displayed;

    }
}
?>
