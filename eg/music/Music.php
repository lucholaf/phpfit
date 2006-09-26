<?php

class Music {
    
	public static $status = "ready";
    
    public $title;
    public $artist;
    public $album;
    public $genre;
    public $size;
    public $seconds;
    public $trackNumber;
    public $trackCount;
    public $year;
    public $date;
    public $selected = false;
    
    
    public function track() {
        return $this->trackNumber . " of " . $this->trackCount;
    }
    
    public function time() {
        return round($this->seconds / 0.6) / 100.0;
    }
    
	public static function parse($string) {
		$m = new Music();
		$t = split("\t", $string);
		
		$m->title = $t[0];
		$m->artist = $t[1];
		$m->album = $t[2];
		$m->genre = $t[3];
		$m->size = $t[4];
		$m->seconds = $t[5];
		$m->trackNumber = $t[6];
		$m->trackCount = $t[7];
		$m->year = $t[8];
		$m->date = $t[9];
		
		return $m;
        
	}

	public $typeDict = array(
    "title" => "string",
    "artist" => "string",
    "album" => "string",
    "year" => "string",
    "genre" => "string",
    "size" => "string",
    "date" => "string",
    "time()" => "double",
    "track()" => "string"
	);    
}
?>
