<?PHP
/**
 * FIT custom exception
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
 * FIT custom exception: FileIO
 *
 * $e = new PHPFIT_Exception_FileIO( 'Message', 'path/to/file' );
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage Exception
 */
 
class PHPFIT_Exception_Parse extends Exception 
{
   /**
    * Exception string offset of parser
    * @var string
    */
    protected $offset = 0;

   /**
    * constructor
    * 
    * @param string $message exception message
    * @param string $offset
    * @see Exception
    */
    public function __construct( $msg, $offset )
    {
        $this->offset = $offset;
        $this->message = $msg;
        parent::__construct($this->message);
    }   
    
   /**
    * receive offset
    * @return int parser offset
    */
    public function getOffset() 
    {
        return $this->offset;
    }

   /**
    * output as string
    * @return string of error message including offest
    */
    public function __toString() {
        return $this->message .' at ' . $this->offset;
    }
}
?>