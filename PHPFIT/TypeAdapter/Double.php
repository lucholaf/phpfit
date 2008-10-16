<?php
class PHPFIT_TypeAdapter_Double extends PHPFIT_TypeAdapter
{

    const PRECISION = 0.000001;

    public function equals($a, $b)
    {
        return $this->doubleEquals($a, $b);
    }

    public function doubleEquals($a, $b)
    {
        if (abs($b - $a) < self::PRECISION) {
            return true;
        }
        return false;
    }

    public function parse($s)
    {
        return doubleval($s);
    }
}

