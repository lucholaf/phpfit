<?php

class PHPFIT_TypeAdapter_ScientificDouble extends PHPFIT_TypeAdapter {
	public function parse($s) {
        return PHPFIT_ScientificDouble::valueOf( $s );
	}
    
}

?>
