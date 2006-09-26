<?php

require_once 'PHPFIT/Fixture.php';
require_once 'MusicLibrary.php';
require_once 'MusicPlayer.php';

class Browser extends PHPFIT_Fixture {
    
	// Library //////////////////////////////////	
    
	/**
    * @param string path
    */
    
	public function library($path) {
		MusicLibrary::load($path);
	}
    
	public function totalSongs() {
		return count(MusicLibrary::$library);
	}
    
	// Select Detail ////////////////////////////

    public function playing() {
        return MusicPlayer::$playing->title;
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
        MusicPlayer::play(MusicLibrary::$looking);
    }
    
    public function status() {
        return Music::$status;
    }
    
    public function pause() {
        MusicPlayer::pause();
    }
    
    public function remaining() {
         return MusicPlayer::minutesRemaining();
    }
    
    public function selectedSongs() {
        return MusicLibrary::displayCount();
    }
    
    public function sameAlbum() {
        MusicLibrary::findAlbum(MusicLibrary::$looking->album);
    }    
    
    public function sameArtist() {
        MusicLibrary::findArtist(MusicLibrary::$looking->artist);
    }
    
    public function showAll() {
        MusicLibrary::findAll();
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
    "selectedSongs()" => "int",	
	);
}
?>