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
	
	
	/**
    * @param Music $m 
    */	
    public static function select($m) {
        self::$looking = $m;
    }
    
    
    public static function displayContents() {
    }
}
?>
