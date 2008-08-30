<?php

require_once 'PHPFIT/Comparable.php';

class PHPFIT_ScientificDouble implements PHPFIT_Comparable {

    protected $value = 0.0;
    protected $precisionValue = 0.0;

    function __construct($value) {
        $this->value = $value;
    }

   /**
    * @param mixed $other
    * @return boolean
    */
    function equals($other) {
        return $this->compareTo($other) == 0;
    }

   /**
    * look at interface Comparable
    */
    public function compareTo($other) {
        $other = floatval($other);
        $diff = $this->value - $other;
        if ($diff < -$this->precisionValue) return -1;
        if ($diff > $this->precisionValue) return 1;
        return 0;
    }

   /**
    * @param string $s
    * @return PHPFIT_ScientificDouble
    */
    public static function valueOf($s) {
        $result = new PHPFIT_ScientificDouble(floatval($s));
        $result->precisionValue = self::precision($s);
        return $result;
    }

   /**
    * @param string $s
    * @return double
    */
    public static function precision($s) {
        $value = floatval($s);
        $bound = floatval(self::tweak($s));
        return abs($bound - $value);
    }


   /**
    * @param string $s
    * @return string
    */
    public static function tweak($s) {
        $pos = strpos(strtolower($s), 'e');

        if ($pos !== false) {
            $start = substr($s, 0, $pos);
            $end = substr($s, $pos);
            return self::tweak($start) . $end;
        }

        if (strpos($s, '.') !== false) {
            return $s . "5";
        }
        return $s . ".5";
    }

   /**
    * @return string
    */
    public function toString() {
        return strval( $this->value );
    }
}
?>