<?php

abstract class PHPFIT_HtmlRenderer_Base
{
	protected $cssTypes = array('passed', 'failed', 'ignored', 'error',
		'gray', 'info', 'label');


	/**
	 * @param string $type
	 * @return string
	 */
	public abstract function getCssProperty($type);

	/**
	 * @param string $type
	 * @param string $string
	 * @return string
	 */
	public function getSpan($type, $string)
	{
		return sprintf(' <span%s>%s</span>', $this->getCssProperty($type), self::escape($string));
	}

	/**
	 * @param string $type
	 * @return void
	 * @throws Exception
	 */
	protected function checkCssType($type)
	{
	    if (!in_array($type, $this->cssTypes)) {
	        throw new Exception('Unknown CSS property type.');
	    }
	}

    /**
     * @param string $string
     * @return string
     */
    public static function escape($string)
    {
        $string = str_replace('&', '&amp;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('  ', ' &nbsp;', $string);
        $string = str_replace('\r\n', '<br />', $string);
        $string = str_replace('\r', '<br />', $string);
        $string = str_replace('\n', '<br />', $string);
        return $string;
    }
}
