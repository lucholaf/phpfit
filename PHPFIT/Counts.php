<?php

class PHPFIT_Counts {
    
    /**
    * var int
    */
	public $right = 0;
	public $wrong = 0;
	public $ignores = 0;
	public $exceptions = 0;
	
	
	/**
    * @return string
    */	
	public function toString() {
		return $this->right . " right, " . $this->wrong . " wrong, "
        . $this->ignores . " ignored, " . $this->exceptions . " exceptions";
	}
	
	
	/**
    * @param PHPFIT_Counts $source
    */	 
	public function tally($source) {
		$this->right .= $source->right;
        $this->wrong .= $source->wrong;
        $this->ignores .= $source->ignores;
        $this->exceptions .= $source->exceptions;
	}
}
?>