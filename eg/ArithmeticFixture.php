<?php

# Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
# Released under the terms of the GNU General Public License version 2 or later.
#
# PHP5 translation by Luis A. Floreani <luis.floreani@gmail.com>

require_once 'PHPFIT/Fixture/Primitive.php';

class eg_ArithmeticFixture extends PHPFIT_Fixture_Primitive {
	public $x = 0;
	public $y = 0;
	
	public function doRows( $rows ) {
		parent::doRows( $rows->more );
	}

	public function doCell( $cell ) {
		
		switch( $cell->count ) {
			case 0: 
				$this->x = intval($cell->text());
				break;
			case 1: $this->y = intval($cell->text()); 
				break;
			case 2: 
				$this->check($cell, intval($this->x+$this->y)); break;
			case 3: 	
				$this->check($cell, intval($this->x-$this->y)); break;
			case 4: 
				$this->check($cell, intval($this->x*$this->y)); break;
			case 5: 	
				if( $this->y == 0 ) {
					throw new Exception( 'ArithmeticException: / by zero' );
                }               
				$this->check($cell, intval($this->x / $this->y));
				break;
			default: break;
		}
	}

}

?>
