<?php

class PHPFIT_Exception_LoadFixture extends Exception
{
    
    /**
     * Exception with filename
     * @var  string
     */
    private $filename;

    /**
     * constructor
     *
     * @param string $fiename
     * @see Exception
    */
    function __construct($filename)
    {
        parent::__construct(sprintf('Could not load fixture from file %s.', $filename));
        $this->filename = $filename;
    }

    /**
     * receive filename
     *
     * @return string name of the file
     */
    function getFilename()
    {
        return $this->filename;
    }
}
