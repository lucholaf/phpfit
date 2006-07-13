<?php

# Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
# Released under the terms of the GNU General Public License version 2 or later.
#
# PHP5 translation by Luis A. Floreani <luis.floreani@gmail.com>

require_once 'PHPFIT/Fixture/Column.php';
require_once 'PHPFIT/ScientificDouble.php';

class Calculator extends PHPFIT_Fixture_Column 
{
	public $volts = 0.0;
	public $key = "";
	
	public $hp;
	
	function __construct() {
		$this->hp = new HP35();
	}

	public function execute() {
		if ($this->key != "")
			$this->hp->key($this->key);
	}

	public function points() {
		return false;
	}
	
	public function flash() {
		return false;
	}
	
	public function watts() {
		return 0.5;
	}
	
	public function x() {
		return new PHPFIT_ScientificDouble($this->hp->r[0]);
	}
	
	public function y() {
		return new PHPFIT_ScientificDouble($this->hp->r[1]);
	}
	
	public function z() {
		return new PHPFIT_ScientificDouble($this->hp->r[2]);
	}
	
	public function t() {
		return new PHPFIT_ScientificDouble($this->hp->r[3]);
	}

	public $typeDict = array(
        "key" => "string",
		"volts" => "double",
		"points()" => "boolean",
		"flash()" => "boolean",
		"watts()" => "double",
		"x()" => "ScientificDouble",
		"y()" => "ScientificDouble",
		"z()" => "ScientificDouble",
		"t()" => "ScientificDouble"
	);	
	
}


class HP35 {
	public $r;
	public $s = 0;
	
	function __construct() {
		$this->r = array (0,0,0,0);
	}
	
	public function key($key) {
		if ($this->numeric($key)) {$this->pushValue(floatval($key));}
		else if ($key == "+") {$this->pushValue($this->pop() + $this->pop());}
		else if ($key == "-") {$t=$this->pop(); $this->push($this->pop()-$t);}
		else if ($key == "*") {$this->pushValue($this->pop() * $this->pop());}
		else if ($key == "enter") {$this->push();}
		else if ($key == "clx") {$this->r[0] = 0;}
		else if ($key == "/") {
			$t = $this->pop();
			if ($t != 0)
				$this->pushValue($this->pop()/$t);
		}
		else if ($key == "x^y") {$this->pushValue(exp(log($this->pop())*$this->pop()));}
		else if ($key == "clr") {$this->r[0] = 0;$this->r[1] = 0;$this->r[2] = 0;$this->r[3] = 0;}
		else if ($key == "chs") {$this->r[0] = -$this->r[0];}
		else if ($key == "sin") {$this->pushValue(sin(deg2rad($this->pop())));}
		else if ($key == "chs") {$this->r[0] = -$this->r[0];}
		else 
			throw new Exception("can't do key: " . $key);
            
	}
	
	public function numeric($key) {
		return is_numeric($key);
	}
	
	public function push() {
    	for ($i=3; $i>0; $i--) {
        	$this->r[$i] = $this->r[$i-1];
        }
    }
    
    public function pop() {
    	$result = $this->r[0];
        for ($i=0; $i<3; $i++) {
        	$this->r[$i] = $this->r[$i+1];
        }
        return $result;
    }
    
    public function pushValue($value) {
   		$this->push();
        $this->r[0] = $value;
    }
	
}

?>