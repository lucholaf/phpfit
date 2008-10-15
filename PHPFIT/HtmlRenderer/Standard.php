<?php
require_once 'PHPFIT/HtmlRenderer/Base.php';

class PHPFIT_HtmlRenderer_Standard extends PHPFIT_HtmlRenderer_Base
{
    /**
     * make the include folder available for user's fixtures
     * @var array
     */
    protected $backgroundColor = array(
	    'passed'    => '#cfffcf',
	    'failed'    => '#ffcfcf',
	    'ignored'   => '#efefef',
	    'error'     => '#ffffcf',
    );
    
    protected $style = array(
    	'info'  => 'color:#808080;',
    	'label' => 'color:#c08080;font-style:italic;font-size:small;',
    );

	/**
	 * @param string $type
	 * @return string
	 */
	public function getCssProperty($type)
	{
		$this->checkCssType($type);
		if ($this->hasBackgroundColor($type)) {
			return sprintf(' bgcolor="%s"', $this->backgroundColor[$type]);
		}
		if ($this->hasStyle($type)) {
		    return sprintf(' style="%s"', $this->style[$type]);
		}
		if ($type == 'gray') {
		    return ' class="fit_grey"';
		}
		if ($type == 'stacktrace') {
		    return ' class="fit_stacktrace"';
		}
	}

	protected function hasBackgroundColor($type)
	{
	    return array_key_exists($type, $this->backgroundColor);
	}

	protected function hasStyle($type)
	{
	    return array_key_exists($type, $this->style);
	}
}
