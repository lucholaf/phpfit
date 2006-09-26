<?php

require_once 'PHPFIT/Fixture/Column.php';

abstract class PHPFIT_Fixture_Row extends PHPFIT_Fixture_Column {
    
    public function getTargetClass() {} // must be overridden in your subclass
    public function query() {} // must be overridden in your subclass
    
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
            $this->match($this->buildArrayFromParser($rows->more), $results, 0);
        } catch (Exception $e) {
            $this->exception($rows->leaf(), $e);
        }
    }
    
    /**
    * Travel each column and check each cell
    *
    * @param array $expected
    * @param array $expected
    * @param integer $col
    */
    protected function match($expected, $computed, $col) {
        $eColumn = $this->eSort($expected, $col); // expected column
        $cColumn = $this->cSort($computed, $col); // computed column
        $keys = array_keys($eColumn) + array_keys($cColumn); // union
        foreach ($keys as $key) {
            $eList = $eColumn[$key];
            $cList = $cColumn[$key];
            $this->checkCell($eList, $cList);
        }
    }

    
    /**
    * @param array $eList
    * @param array $cList
    */
    public function checkCell($eList, $cList) {
        if (count($eList) == 0) {
            //surplus.addAll(cList);
            return;
        }
        if (count($cList) == 0) {
            //missing.addAll(eList);
            return;
        }
        
        $parse = array_shift($eList); 
        $obj = array_shift($cList);
        $cell = $parse->parts;
        
        for($i = 0; $i < count($this->columnBindings) && $cell != null; $i++) {
            $adapter = $this->columnBindings[$i];
            if ($adapter != null) {
                $adapter->target = $obj;
            }
            parent::checkCell($cell, $adapter);
            $cell = $cell->more;
        }
        $this->checkCell($eList, $cList);
    }
    
    /**
    * @param PHPFIT_Parse $rows
    * @return array
    */
    protected function buildArrayFromParser($rows) {
        $array = array();
        while ($rows != null) {
            $array[] = $rows;
            $rows = $rows->more;
        }
        return $array;
    }
    
    /**
    * @param array $expected: array of PHPFIT_Parse objects
    * @param int $col
    * @return array
    */
    protected function eSort($expected, $col) {
        $adapter = $this->columnBindings[$col];
        $result = array();
        
        foreach ($expected as $row) {
            $cell = $row->parts->at(0);
            try {
                $key = $adapter->parse($cell->text());
                $result[$key][] = $row;
            } catch (Exception $e) {
                $this->exception($cell, $e);
                $rest = $cell->more;
                while ($rest != null) {
                    $this->ignore($rest);
                    $rest = $rest->more;
                }
            }
        }
        return $result;
    }

    /**
    * @param array $computed
    * @param int $col
    * @return array
    */
    protected function cSort($computed, $col) {
        $adapter = $this->columnBindings[$col];
        $result = array();
        
        foreach ($computed as $row) {
            try {
                $adapter->target = $row;
                $key = $adapter->get();
                $result[$key][] = $row;
            } catch(Exception $e) {
                //surplus!
            }
        }
        return $result;
        
    }

}


?>