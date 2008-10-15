<?php
require_once 'PHPFIT/HtmlRenderer/Base.php';

class PHPFIT_HtmlRenderer_Fitnesse extends PHPFIT_HtmlRenderer_Base
{
    protected $cssClass = array(
	    'passed'    => 'pass',
	    'failed'    => 'fail',
	    'ignored'   => 'ignore',
	    'error'     => 'error',
    	'info'  => 'fit_info',
    	'label' => 'fit_label',
    	'gray' => 'fit_grey',
    	'stacktrace' => 'fit_stacktrace',
    );

	/**
	 * @param string $type
	 * @return string
	 */
	public function getCssProperty($type)
	{
		$this->checkCssType($type);
		return sprintf(' class="%s"', $this->cssClass[$type]);
	}
}
