<?php

require_once "eg/ArithmeticColumnFixtureBase.php";

class ArithmeticColumnFixture extends ArithmeticColumnFixtureBase {

    public $typeDict = array(
    "x" => "integer",
    "y" => "integer",
    "plus()" => "integer",
    "minus()" => "integer",
    "times()" => "integer",
    "divide()" => "integer",
    "floating()" => "double"
    );

}

?>
