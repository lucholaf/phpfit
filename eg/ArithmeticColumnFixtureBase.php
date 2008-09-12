<?php

require_once 'PHPFIT/Fixture/Column.php';

/**
 * Base class for ArithmeticColumnFixture without the typeDict property
 */
abstract class ArithmeticColumnFixtureBase extends PHPFIT_Fixture_Column {

    public $x = 0;
    public $y = 0;

    public function plus() {
        return intval($this->x + $this->y);
    }

    public function minus() {
        return intval($this->x - $this->y);
    }

    public function times() {
        return intval($this->x * $this->y);
    }

    public function divide() {
        if ($this->y == 0) {
            throw new Exception("ArithmeticException: / by zero");
        }
        return intval($this->x / $this->y);
    }

    public function floating() {
        if ($this->y == 0) {
            throw new Exception("ArithmeticException: / by zero");
        }
        return floatval($this->x / $this->y);
    }

        /*
        public ScientificDouble  sin () {
                return new ScientificDouble(Math.sin(Math.toRadians(x)));
        }
        */

}

?>
