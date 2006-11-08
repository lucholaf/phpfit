<?php

require_once 'PonderadorFecha.php';

class CatalogoPrecios {
	
	private $catalogo = array(
		'aconcagua' 	=> 100,
		'hyatt' 		=> 200,
		);
		
	public function obtenerPrecio($hotel) {
		if (isset($this->catalogo[$hotel]))
			return $this->catalogo[$hotel];
	}
}

?>