<?php

class PHPFIT_Fixture_Primitive extends PHPFIT_Fixture {
    
    /**
    * parse string ans expect a floating point value
    * 
    * @param PHPFIT_Parse $cell
    * @return double
    */	
	private function parseLong( $cell ) {
		if( strpos( $cell->text(), '.' ) === false ) {
			throw new Exception( 'NumberFormatException: For input string: "' . $cell->text(). '"');
        }
        
		return doubleval($cell->text());
	}
	
	/**
    * @param PHPFIT_Parse $cell
    * @param string/int $value
    */
    public function check( $cell, $value ) {
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
