<?php

require_once 'PHPFIT/Parse.php';
require_once 'PHPFIT/Fixture/Action.php';

class PHPFIT_Fixture_TimedAction extends PHPFIT_Fixture_Action
{

    /**
    * @param PHPFIT_Parse $table
    */
    public function doTable($table)
    {
        parent::doTable($table);
/*        
        $table->parts->parts->last()->more = $this->td("time");
        $table->parts->parts->last()->more = $this->td("split");
*/
    }

    /**
    * @param PHPFIT_Parse $cells
    */
    public function doCells($cells)
    {
        $start  = $this->theTime();
        parent::doCells($cells);
/*        
        $cells->last()->more = $this->td(date('H:m:s', $start));
        $split = $this->theTime() - $start;
        if ($split < 1.0) {
            $text = "&nbsp;";
        } else {
            $text = $split;
        }
        $cells->last()->more = $this->td($text);
*/
    }

    /**
    * @return int: seconds
    */
    public function theTime()
    {
        return time();
    }

    /**
    * @param string $body
    * @return PHPFIT_Parse
    */
    public function td($body)
    {
        return PHPFIT_Parse::createSimple("td", $this->infoInColor($body), null, null);
    }
}

