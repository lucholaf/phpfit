<?php

require_once 'PHPFIT/Fixture/Column.php';

abstract class PHPFIT_Fixture_Row extends PHPFIT_Fixture_Column {
    
    function getTargetClass() {}
    function query() {}
    
    /**
    * Process a table's row
    * 
    * @param PHPFIT_Parse $rows
    */	
    public function doRows( $rows ) {
        // bind the first row (heads) to function and properties
        $this->bind( $rows->parts );
    }
}

?>