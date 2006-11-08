<?php

require_once 'PHPFIT/Fixture/Column.php';
require_once 'eg/hoteles/model/PonderadorFecha.php';

class Temporada extends PHPFIT_Fixture_Column {
	public $fecha;
	
	public function ponderacion() {
		$ponderador = new PonderadorFecha();
		return $ponderador->obtenerPonderacion($this->fecha);
	}

	public $typeDict = array(
		"fecha" 		=> "string",
		"ponderacion()" => "double",
	);	
}

?>