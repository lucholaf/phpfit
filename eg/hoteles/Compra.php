<?php

require_once 'eg/hoteles/model/Carrito.php';

class Compra extends PHPFIT_Fixture {

	private static $carrito;
	
	public function cargarCarrito() {
		self::$carrito = new Carrito();
		self::$carrito->agregarItem(new Item('hotel A', '2006-12-21'));
		self::$carrito->agregarItem(new Item('hotel A', '2006-12-22'));
		self::$carrito->agregarItem(new Item('hotel A', '2006-12-23'));
		self::$carrito->agregarItem(new Item('hotel A', '2006-12-24'));
	}
	
	public function totalItemsCarrito() {
		return self::$carrito->totalItems();
	}
	
	public function comprarItem($index) {
		self::$carrito->comprar($index-1);
	}
	
	public function precioTotal() {
		return self::$carrito->precioTotal();
	}
	
	public static function obtenerItemsCarrito() {
		if (self::$carrito) {
			return self::$carrito->obtenerItems();
		}
	}
	
	public static function obtenerItemsComprados() {
		if (self::$carrito) {
			return self::$carrito->obtenerItemsComprados();
		}
	}
	
	public $typeDict = array (
		"totalItemsCarrito()" 	=> "int",
		"precioTotal()" 		=> "int",
	);
}

?>