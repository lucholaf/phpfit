<?php

require_once "eg/ArithmeticColumnFixtureBase.php";

class ArithmeticColumnFixtureWithStaticTypeDict extends ArithmeticColumnFixtureBase {

    public static $typeDict = array(
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
