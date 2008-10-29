<?php
require_once 'PHPFIT/Exception/ClassHelper.php';

class PHPFIT_Exception_ClassHelper_MissingProperty extends PHPFIT_Exception_ClassHelper
{
	/**
	 * @param string $class
	 * @param string $property
	 * @return void
	 */
	public function __construct($class, $property)
	{
	    parent::__construct(sprintf('Property does not exist! %s::%s', $class, $property));
	}
}
