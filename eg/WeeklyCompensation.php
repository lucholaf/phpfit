<?php
require_once 'PHPFIT/Fixture/Column.php';

require_once "eg/WeeklyTimesheet.php";

class eg_WeeklyCompensation extends PHPFIT_Fixture_Column {
	public $StandardHoras = 0;
	public $VacacionesHoras = 0;
	public $SalarioHora = 0;
		
	public function Pago() {
		$timesheet = new WeeklyTimesheet($this->StandardHoras, $this->VacacionesHoras);
		return $timesheet->calculatePay($this->SalarioHora);
	}
	
	public function TotalHoras() {
		$timesheet = new WeeklyTimesheet($this->StandardHoras, $this->VacacionesHoras);
		$timesheet->calculatePay($this->SalarioHora);
		return $timesheet->getTotalHours();
	}
	
	public $typeDict = array(
		"StandardHoras" => "integer",
		"VacacionesHoras" => "integer",
		"SalarioHora" => "integer",
		"Pago()" => "integer",
		"TotalHoras()" => "integer"
	);	
}

?>
