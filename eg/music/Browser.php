<?php


# Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
# Released under the terms of the GNU General Public License version 2 or later.
#
# PHP5 translation by Luis A. Floreani <luis.floreani@gmail.com>

require_once ("config.php");
require_once (PHPFIT_DIR.LIB_FIT_DIR."Fixture.php");
require_once ("MusicLibrary.php");

class Browser extends Fixture {

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

	public function select($i) {
		MusicLibrary :: select(MusicLibrary :: $library[$i -1]);
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
			
	public $typeDict = array (
		"totalSongs" => "integer",
		"title" => "string",
		"artist" => "string",
		"album" => "string",	
		"year" => "string",	
		"time" => "double",	
		"track" => "string",	
	);
}
?>