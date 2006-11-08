<?php

require_once 'PHPFIT/Fixture/Row.php';
require_once 'eg/hoteles/Compra.php';

class CompraLista extends PHPFIT_Fixture_Row {
	
	public function getTargetClass() {
		return new Item();
	}

	public function query() {
		return Compra::obtenerItemsComprados();
	}
}

?>