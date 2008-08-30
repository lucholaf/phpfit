<?php

class PHPFIT_TypeAdapter_ScientificDouble extends PHPFIT_TypeAdapter {

    public function equals($a, $b) {
        return $a->equals( $b->toString() );
    }

    public function parse($s) {
        return PHPFIT_ScientificDouble::valueOf( $s );
    }

}

?>
