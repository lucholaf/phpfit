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
            $this->match($this->buildArrayFromParser($rows->more), $results, 0);
        } catch (Exception $e) {
            $this->exception($rows->leaf(), $e);
        }
    }
    
    protected function match($expected, $computed, $col) {
        $eMap = $this->eSort($expected, $col);
        $cMap = $this->cSort($computed, $col);
        $keys = $this->union(array_keys($eMap), array_keys($cMap));
        foreach ($keys as $key) {
            $eList = $eMap[$key];
            $cList = $cMap[$key];
            $this->checkCell($eList, $cList);
        }

    }

    
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
        
        for($i=0; $i < count($this->columnBindings) && $cell != null; $i++) {
            $adapter = $this->columnBindings[$i];
            if ($adapter != null) {
                $adapter->target = $obj;
            }
            parent::checkCell($cell, $adapter);
            $cell = $cell->more;
        }
        $this->checkCell($eList, $cList);
    }
    
    protected function union($map1, $map2) {
        return $map1 + $map2;
    }
    
    protected function buildArrayFromParser($rows) {
        $array = array();
        while ($rows != null) {
            $array[] = $rows;
            $rows = $rows->more;
        }
        return $array;
    }
    
    protected function eSort($expected, $col) {
        $adapter = $this->columnBindings[$col];
        $result = array();
        
        foreach ($expected as $row) {
            $cell = $row->parts->at(0);
            try {
                $key = $adapter->parse($cell->text());
                $this->bin($result, $key, $row);
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

    protected function cSort($computed, $col) {
        $adapter = $this->columnBindings[$col];
        $result = array();
        
        foreach ($computed as $row) {
            try {
                $adapter->target = $row;
                $key = $adapter->get();
                $this->bin($result, $key, $row);
            } catch(Exception $e) {
                //surplus!
            }
        }
        return $result;
        
    }
    
    protected function bin(&$map, $key, $row) {
        $map[$key][] = $row;
    }


}


?>