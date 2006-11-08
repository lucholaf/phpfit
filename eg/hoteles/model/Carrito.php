<?php

require_once 'CatalogoPrecios.php';
require_once 'PonderadorFecha.php';

class Carrito {
	
	private static $items = null;

	public function __construct() {
		if (!self::$items) {
			self::$items[] = new Item('aconcagua', '2006-12-21');
			self::$items[] = new Item('aconcagua', '2006-12-22');
			self::$items[] = new Item('aconcagua', '2006-12-23');
			self::$items[] = new Item('aconcagua', '2006-12-24');
		}
	}
	
	public function obtenerItem($index) {
		return self::$items[$index];
	}
	
	public function totalItems() {
		return count(self::$items);
	}
	
	public static function obtenerItems() {
		if (self::$items)
			return self::$items;
	}
}

class Item {
	public $hotel;
	public $fecha;
	public $precio;
	
	public function __construct($hotel = null, $fecha = null, $precio = 0) {
		$this->hotel = $hotel;
		$this->fecha = $fecha;
		if ($precio)
			$this->precio = $precio;
		else
			$this->_calcularPrecio();
	}

	public function _calcularPrecio() {
		$catalogo = new CatalogoPrecios();
		$ponderador = new PonderadorFecha();
		$this->precio = $catalogo->obtenerPrecio($this->hotel) * $ponderador->obtenerPonderacion($this->fecha);
	}
	
	public $typeDict = array (
		"hotel" 	=> "string",
		"fecha" 	=> "string",
		"precio" 	=> "int"
		);
}

?>