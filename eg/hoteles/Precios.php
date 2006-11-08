<?php

require_once 'PHPFIT/Fixture/Column.php';
require_once 'eg/hoteles/model/Carrito.php';

class Precios extends PHPFIT_Fixture_Column {
	public $hotel;
	public $fecha;
	
	public function precioNoche() {
		$item = new Item($this->hotel, $this->fecha);
		return $item->precio;
	}	

	public $typeDict = array(
		"hotel" 		=> "string",
		"fecha" 		=> "string",
		"precioNoche()" => "int",
	);	
}

?>