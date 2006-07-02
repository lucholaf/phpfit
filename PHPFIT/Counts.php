<?php

# Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
# Released under the terms of the GNU General Public License version 2 or later.
#
# PHP5 translation by Luis A. Floreani <luis.floreani@gmail.com>

class PHPFIT_Counts {
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
	 * @param Count source
	 */
	 
	public function tally($source) {
		$this->right .= $source->right;
        	$this->wrong .= $source->wrong;
        	$this->ignores .= $source->ignores;
        	$this->exceptions .= $source->exceptions;
	}
}
?>