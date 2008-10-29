<?php
require_once 'PHPFIT/Exception/ClassHelper.php';

class PHPFIT_Exception_ClassHelper_MissingMethod extends PHPFIT_Exception_ClassHelper
{
	/**
	 * @param string $class
	 * @param string $method
	 * @return void
	 */
	public function __construct($class, $method)
	{
	    parent::__construct(sprintf('Method does not exist! %s::%s()', $class, $method));
	}
}
