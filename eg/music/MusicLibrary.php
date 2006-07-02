<?php

# Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
# Released under the terms of the GNU General Public License version 2 or later.
#
# PHP5 translation by Luis A. Floreani <luis.floreani@gmail.com>

require_once("config.php");
require_once("Music.php");

class MusicLibrary {
	
	/**
	 * @var Music 
	 */
	 
	public static $library = array();
	public static $looking = null;
	
		
	public static function load($path) {
		$fp = fopen(PHPFIT_DIR . LIB_DIR . $path, "r");
		
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
