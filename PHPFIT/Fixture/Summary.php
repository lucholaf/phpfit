<?php

class PHPFIT_Fixture_Summary extends PHPFIT_Fixture {
	public static $countKey = "counts";

	public function doTable($table) {
		$this->summary[self::$countKey] = $this->counts;
		ksort($this->summary);
		$table->parts->more = $this->rows(array_keys($this->summary));
	}
	
	
	/**
	 * @param array keys
	 * @return Parse
	 */
	 
	protected function rows($keys) {
		if (count($keys) > 0) {
			$key = $keys[0];
			$obj = $this->summary[$key];
			if (is_string($obj))
				$str = $obj;
			else
				$str = $obj->toString();

			$td1 = $this->td($str, null);
			$td2 = $this->td($key, $td1);
			
			$result = $this->tr($td2, $this->rows(array_splice($keys, 1)));
			
			if ($key == self::$countKey) {
				$this->mark($result);
			}
			return $result;
		} else {
			return null;
		}
	}

	
	/**
	 * @param Parse parts
	 * @param Parse more
	 * @return Parse
	 */
	 
	protected function tr($parts, $more) {
		return new PHPFIT_Parse("tr", null, $parts, $more, true);
	}
	
	
	/**
	 * @param string body
	 * @param Parse more
	 * @return Parse
	 */
	 
	protected function td($body, $more) {
		return new PHPFIT_Parse("td", $this->infoS($body), null, $more, true);
	}
	
	
	/**
	 * @param Parse row
	 */
	 
	protected function mark($row) {
		$official = $this->counts;
		$this->counts = new PHPFIT_Counts();
		$cell = $row->parts->more;
		if (($official->wrong + $official->exceptions) > 0) {
			$this->wrong($cell);
		} else {
			$this->right($cell);
		}
		$this->counts = $official;
	}	
}
?>