<?php

require_once 'PHPFIT/Fixture/Column.php';
require_once 'eg/hoteles/model/Carrito.php';

class Precios extends PHPFIT_Fixture_Column {
	public $nombreHotel;
	public $fecha;
	
	public function precioNoche() {
		$item = new Item($this->nombreHotel, $this->fecha);
		return $item->getPrecio();
	}	

	public $typeDict = array(
		"nombreHotel" 		=> "string",
		"fecha" 			=> "string",
		"precioNoche()" 	=> "int",
	);	
}

?>