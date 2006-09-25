<?php

require_once 'PHPFIT/Fixture/Column.php';

abstract class PHPFIT_Fixture_Row extends PHPFIT_Fixture_Column {
    
    public function getTargetClass() {}
    public function query() {}
    
    /**
    * Process a table's row
    * 
    * @param PHPFIT_Parse $rows
    */	
    public function doRows( $rows ) {
        try {
            // bind the first row (heads) to function and properties
            $this->bind( $rows->parts );            
            $results = $this->query();            
            $this->match($this->buildArray($rows->more), $results, 0);
        } catch (Exception $e) {
            $this->exception($rows->leaf(), $e);
        }
    }
    
    protected function match($expected, $computed, $col) {
        //echo "<br><b>EXPECTED:</b>" . print_r($expected) . "<br>";
        //echo "<br><b>COMPUTED:</b>" . print_r($computed) . "<br>";
        //echo "<br><br>";
    }
    
    protected function buildArray($rows) {
        $array = array();
        while ($rows != null) {
            $array[] = $rows;
            $rows = $rows->more;
        }
        return $array;
    }
}

?>