<?php

require_once 'PHPFIT/Fixture.php';
require_once 'MusicLibrary.php';

class Browser extends PHPFIT_Fixture {
    
	// Library //////////////////////////////////	
    
	/**
    * @param string path
    */
    
	public function library($path) {
		MusicLibrary :: load($path);
	}
    
	public function totalSongs() {
		return count(MusicLibrary :: $library);
	}
    
	// Select Detail ////////////////////////////

    public function playing() {
    }
    
	public function select($i) {
		MusicLibrary :: select(MusicLibrary :: $library[$i-1]);
	}
    
	public function title() {
		return MusicLibrary :: $looking->title;
	}
	
	public function artist() {
		return MusicLibrary :: $looking->artist;
	}
	
	public function album() {
		return MusicLibrary :: $looking->album;
	}
	
	public function year() {
		return MusicLibrary :: $looking->year;
	}
    
	public function time() {
		return MusicLibrary :: $looking->time();
	}
	
	public function track() {
		return MusicLibrary :: $looking->track();
	}
    
    // Play Buttons /////////////////////////////
    
    public function play() {
    }
    
    public function status() {
    }
    
    public function pause() {
    }
    
    public function remaining() {
    }
    
    //sacar!
    public function ok() {
    }
    
    public function sameAlbum() {
    }    
    
    public function sameArtist() {
    }
    
    public function showAll() {
    }
    
	public $typeDict = array (
    "totalSongs()" => "integer",
    "title()" => "string",
    "artist()" => "string",
    "album()" => "string",	
    "year()" => "string",	
    "time()" => "double",	
    "track()" => "string",	
    "status()" => "string",	
    "remaining()" => "string",	
    "playing()" => "string",	
	);
}
?>