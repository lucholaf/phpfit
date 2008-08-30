<?php

class PHPFIT_RunTime {

    /**
    * start time
    * @var double
    */
    private $start;

    /**
    * construtor
    *
    * start timer
    */
    function __construct() {
        $this->start = microtime( true );
    }

    /**
    * receive elapsed time as seconds
    * @return string
    */
    public function toString() {
        return microtime( true ) - $this->start . ' seconds';
    }

    /**
    * interface to ask for current timer state
    *
    * Supports the following properties
    * - start when has the timer been started
    * - elapsed duration until now
    *
    * @param string $name of property
    * @return mixed
    */
    public function __get( $name ) {
        switch( $name ) {
            case 'start':
            return $this->start;
            break;

            case 'elapsed':
            return microtime( true ) - $this->start;
            break;

            default:
            break;
        }

        throw new Exception( 'Property ' . $name . ' is not defined' );
        return null;
    }
}
?>