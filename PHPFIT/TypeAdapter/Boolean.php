<?php

class PHPFIT_TypeAdapter_Boolean extends PHPFIT_TypeAdapter {
    
	public function equals($a, $b) {
		return $a == $b;
	}

	public function parse($s) {
		if ($s == "false") {
			return false;
        } else if ($s == "true") {
            return true;
        }
		return "not a boolean";
	}
    
    public function toString() {
        
        $value = $this->get();
        
        if ($value) {
            return  'true';
        } else {
            return 'false';
        }
    }
}

?>