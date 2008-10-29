<?php
require_once 'PHPFIT/Exception/ClassHelper.php';

class PHPFIT_Exception_ClassHelper_MissingTypeDictEntry
		extends PHPFIT_Exception_ClassHelper
{
	/**
	 * @param string $class
	 * @param string $name
	 * @return void
	 */
	public function __construct($class, $name)
	{
	    $message = sprintf('Property or method has no definition ' .
	    		'in $typeDict! %s::%s', $class, $name);
	    parent::__construct($message);
	}
}
