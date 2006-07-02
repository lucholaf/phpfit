<?PHP
/**
 * FIT RunTime
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @subpackage FileRunner
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */

/**
 * FIT RunTIme
 * 
 * A simple timer class for minor benchmarks
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage Fixture
 */
class PHPFIT_RunTime 
{
   /**
    * start time
    * @var float
    */
    private $start;
    
   /**
    * construtor 
    *
    * start timer
    */
    function __construct() 
    {
        $this->start = microtime( true );
    }

   /**
    * receive elapsed time as seconds
    * @return float $elap
    */
    public function toString() 
    {
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
    public function __get( $name ) 
    {
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