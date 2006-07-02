<?php
/**
 * FIT Fixture: PrimitiveFixture
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @subpackage Fixture
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */

/**
 * FIT Fixture: ActionFixture
 * 
 * A PrimitiveFixture is primitive in that it relies on fundamentals 
 * and is free of adornment. 
 *
 * @version 0.1.0
 * @package FIT
 * @subpackage Fixture
 */
class PHPFIT_Fixture_Primitive extends PHPFIT_Fixture {

   /**
    * parse string ans expect a floating point value
    * 
    * @param Parse cell
    * @return float
    */	
	private function parseLong( $cell ) 
    {
		if( strpos( $cell->text(), '.' ) === false ) {
			throw new Exception( 'NumberFormatException: For input string: "' . $cell->text(). '"');
        }

		return (float) $cell->text();
	}
	
	/**
	 * @param Parse cell
	 * @param string/int value
	 */
	 
    public function check( $cell, $value ) 
    {
        if( !is_numeric( $value ) ) {
            if( $cell->text() == $value ) {
                $this->right( $cell );
                return;
            }
            
            $this->wrong($cell, $value);            
            return;
        }
        
        // integer values
        if( is_int( $value ) ) {
            $v  = (int) $cell->text();
            if( $v == $value ) {
                $this->right( $cell );
                return;
            }
            $this->wrong( $cell, $value );
            return;
        }
        
        // floating point number
        $v  = $this->parseLong( $cell );
        if( $v == $value ) {
            $this->right( $cell );
            return;
        }
        $this->wrong($cell, $value);            
        return;
	}
}
?>