<?php

class PHPFIT_Exception_FileIO extends Exception
{
    
    /**
    * Exception with filename
    * @var  string
    */
    private $filename;

    /**
    * constructor
    *
    * @param string $message exception message
    * @param string $fiename
    * @see Exception
    */
    function __construct($msg, $filename)
    {
        $this->message = $msg;
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
