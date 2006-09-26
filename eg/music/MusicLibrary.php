<?php

require_once 'Music.php';

class MusicLibrary {
	
	/**
    * @var Music 
    */
	public static $library = array();
	public static $looking = null;
	
    
	public static function load($path) {
        self::$library = null;
        self::$looking = null;
        
		$fp = @fopen($path, "r", true);
		
        if ($fp === false) {            
            die("error loading file: " . $path);
        }
        
		fgets($fp); // skip column headings
		
		while ($line = fgets($fp)) {
			self::$library[] = Music::parse($line);
		}
        
		fclose($fp);
	}

    public static function searchComplete() {
        Music::$status = (MusicPlayer::$playing == null) ? "ready" : "playing";
    }	

    public static function search($seconds){
        Music::$status = "searching";
        Simulator::$nextSearchComplete = Simulator::schedule($seconds);
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
    
    public static function findAll() {
        self::search(3.2);
        for ($i=0; $i < count(self::$library); $i++) {
            self::$library[$i]->selected = true;
        }
    }
    
    public static function displayCount() {
        $count = 0;
        for ($i=0; $i < count(self::$library); $i++) {
            $count += (self::$library[$i]->selected ? 1 : 0);
        }
        return $count;
    } 
    
    public static function findArtist($a) {
        self::search(2.3);
        for ($i=0; $i < count(self::$library); $i++) {
            self::$library[$i]->selected = (self::$library[$i]->artist == $a);
        }
    }
    
    public static function findAlbum($a) {
        self::search(1.1);
        for ($i=0; $i < count(self::$library); $i++) {
            self::$library[$i]->selected = (self::$library[$i]->album == $a);
        }
    }    
}
?>
