<?php

# Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
# Released under the terms of the GNU General Public License version 2 or later.
#
# PHP5 translation by Luis A. Floreani <luis.floreani@gmail.com>

require_once 'PHPFIT/Fixture/Column.php';

class eg_ArithmeticColumnFixture extends PHPFIT_Fixture_Column 
{
	public $x = 0;
	public $y = 0; // not 0 to avoid that getReturnType (for floating() and divide()) throws an Exception
	
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
		if ($this->y == 0)
			throw new Exception("ArithmeticException: / by zero");
		return intval($this->x / $this->y);
	}

	public function floating() {
		if ($this->y == 0)
			throw new Exception("ArithmeticException: / by zero");
		return floatval($this->x / $this->y);
	}

	/*
	public ScientificDouble  sin () {
		return new ScientificDouble(Math.sin(Math.toRadians(x)));
	}
	*/
	
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