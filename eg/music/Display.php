<?php

require_once 'PHPFIT/Fixture/Row.php';
require_once 'eg/music/Music.php';


class Display extends PHPFIT_Fixture_Row {

    public function getTargetClass() {
        return  new Music();
    }

    public function query() {
        return MusicLibrary::displayContents();
    }
}

?>
