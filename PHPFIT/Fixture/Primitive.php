<?php

require_once 'PHPFIT/Fixture.php';

class PHPFIT_Fixture_Primitive extends PHPFIT_Fixture {
	
    /**
    * @param mixed $value
    * @return integer
    */
    protected function parseInteger($value) {
        if (!is_int($value)) {
            throw new Exception( 'ArithmeticException: invalid value for integer');
        }
        return intval($value);
    }
    
	/**
    * @param PHPFIT_Parse $cell
    * @param string/int $value
    */
    public function check( $cell, $value ) {
        if( $cell->text() == strval($value)) {
            $this->right( $cell );            
        } else {        
            $this->wrong($cell, $value);
        }
	}
}

?>
