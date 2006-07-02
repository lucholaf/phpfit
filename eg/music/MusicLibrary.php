<?php

require_once 'Music.php';

class MusicLibrary {
	
	/**
	 * @var Music 
	 */
	 
	public static $library = array();
	public static $looking = null;
	
		
	public static function load($path) {
		$fp = fopen($path, "r");
		
		fgets($fp); // skip column headings
		
		while ($line = fgets($fp)) {
			self::$library[] = Music::parse($line);
		}
		//var_dump(self::$library);
		fclose($fp);
	}
	
	
	/**
	 * @param Music m 
	 */
	 	
    static function select($m) {
        self::$looking = $m;
    }	
}
?>
