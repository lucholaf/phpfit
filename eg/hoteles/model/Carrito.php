<?php

require_once 'CatalogoPrecios.php';
require_once 'PonderadorFecha.php';

class Carrito {
	
	private $items = null;
	private $itemsComprados = null;
	
	public function agregarItem($item) {
		$this->items[] = $item;
	}
	
	public function obtenerItem($index) {
		return $this->items[$index];
	}
	
	public function totalItems() {
		return count($this->items);
	}
	
	public function obtenerItems() {
		if ($this->items)
			return $this->items;
	}
	
	public function obtenerItemsComprados() {
		if ($this->itemsComprados)
			return $this->itemsComprados;	
	}
	
	public function comprar($index) {
		$this->itemsComprados[] = $this->items[$index];
	}
	
	public function precioTotal() {
		if (!$this->itemsComprados)
			return 0;
			
		$total = 0;
		foreach ($this->itemsComprados as $item) {
			$total += $item->getPrecio();
		}
		return $total;
	}
}

class Item {
	
	private $hotel;
	private $fecha;	
	private $precio = null;
	
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
	
	public function getPrecio() {
		return $this->precio;
	}
	
	public function getHotel() {
		return $this->hotel;
	}
	
	public function getFecha() {
		return $this->fecha;
	}
	
	public $typeDict = array (
		"getHotel()" 	=> "string",
		"getFecha()" 	=> "string",
		"getPrecio()" 	=> "int"
		);
}

?>