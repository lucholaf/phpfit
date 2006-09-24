<?php

require_once 'PHPFIT/Parse.php';

class PHPFIT_Fixture_ActionTimed extends PHPFIT_Fixture_Action {
    
    public function doTable($table) {
        parent::doTable($table);
        $table->parts->parts->last()->more = $this->td("time");
        $table->parts->parts->last()->more = $this->td("split");
        
    }
    
    public function doCells($cells) {
        $start  = $this->time();
        parent::doCells($cells);
        $cells->last()->more = $this->td($start);
        $split = $this->time() - $start;
        if ($split < 1.0) {
            $text = "&nbsp;";
        } else {
            $text = $split;
        }
        $cells->last()->more = $this->td($text);

    }    
    
    public function time() {
        return date('H:m:s');
    }
    public function td($body) {
        return PHPFIT_Parse::createSimple("td", $this->infoInColor($body), null, null);
    }
}

?>