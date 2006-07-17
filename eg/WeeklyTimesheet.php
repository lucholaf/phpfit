<?php

class WeeklyTimesheet {
    
	public $standardHours;
	public $holidayHours;
	public $wage;
	
	public function __construct($sHours, $hHours) {
		$this->standardHours = $sHours;
		$this->holidayHours = $hHours;
	}
	
	public function calculatePay($wage) {
		if ($wage < 0) {
            throw new Exception("Wage can't be negative");
        }
        
		if ($this->standardHours < 0 || $this->holidayHours < 0) {
            throw new Exception("Hours can't be negative");
        }
        
		$extra = 0;
		$tempHours = $this->standardHours;
		if ($this->standardHours > 40) {
			$extra = $this->standardHours - 40;
			$tempHours = 40;
		}
		return $wage * ($tempHours + $extra * 1.5 + $this->holidayHours * 2);	
        
	}
	
	public function getTotalHours() {
		if ($this->standardHours < 0 || $this->holidayHours < 0) {
            throw new Exception("Hours can't be negative");
        }
		return $this->standardHours + $this->holidayHours;
	}
	
}

?>