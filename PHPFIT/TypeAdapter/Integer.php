<?php

class PHPFIT_TypeAdapter_Integer extends PHPFIT_TypeAdapter
{

    public function equals($a, $b)
    {
        return $a === $b;
    }

    public function parse($s)
    {
        return intval($s);
    }
}

