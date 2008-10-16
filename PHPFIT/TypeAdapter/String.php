<?php

class PHPFIT_TypeAdapter_String extends PHPFIT_TypeAdapter
{

    public function equals($a, $b)
    {
        return strcmp($a, $b) == 0;
    }

    public function parse($s)
    {
        return $s;
    }
}

