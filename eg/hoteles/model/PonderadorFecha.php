<?php

class PonderadorFecha {

	public function obtenerPonderacion($fecha) {
		if (strtotime($fecha) < strtotime('2006-12-23'))
			return 1.0;
		else
			return 1.25;
	}
}

?>