<?php

require_once 'PonderadorFecha.php';

class CatalogoPrecios {
	
	private $catalogo = array(
		'hotel A'	=> 100,
		'hotel B' 	=> 200,
		);
		
	public function obtenerPrecio($hotel) {
		if (isset($this->catalogo[$hotel]))
			return $this->catalogo[$hotel];
	}
}

?>