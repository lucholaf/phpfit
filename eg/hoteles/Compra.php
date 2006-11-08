<?php

require_once 'eg/hoteles/model/Carrito.php';

class Compra extends PHPFIT_Fixture {

	private static $carrito;
	private static $itemsComprados;
	
	public function cargarCarrito() {
		self::$carrito = new Carrito();
	}
	
	public function totalItemsCarrito() {
		return self::$carrito->totalItems();
	}
	
	public function comprar($index) {
		$itemNuevo = self::$carrito->obtenerItem($index);
		self::$itemsComprados[] = $itemNuevo;
	}
	
	public function precioTotal() {
		if (!self::$itemsComprados)
			return 0;
			
		$total = 0;
		foreach (self::$itemsComprados as $item) {
			$total += $item->precio;
		}
		return $total;
	}
	
	public static function obtenerItemsCarrito() {
		if (self::$carrito) {
			return self::$carrito->obtenerItems();
		}
	}
	
	public static function obtenerItemsComprados() {
		if (self::$itemsComprados)
			return self::$itemsComprados;
	}
	
	public $typeDict = array (
		"totalItemsCarrito()" 	=> "int",
		"precioTotal()" 		=> "int",
	);
}

?>