<?php

class PonderadorFecha {

	/**
	* valor por default de la ponderacion
	*/
	private $DEFAULT_VALUE = 1.0;
	
	/**
	* Factor multiplicativo que afecta la ponderacion
	*/
	private $FACTOR = 1.20;
	
	/**
	* @param Date $fecha
	* @return float
	*/
	public function obtenerPonderacion($fecha) {
		if (strtotime($fecha) < strtotime('2006-12-23'))
			return $this->DEFAULT_VALUE;
		else
			return $this->DEFAULT_VALUE * $this->FACTOR;
	}
}

?>