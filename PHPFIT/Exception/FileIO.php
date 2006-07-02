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
    function __construct( $msg, $filename ) 
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
?>