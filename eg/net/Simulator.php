<?php
/**
 * FIT Fixture
 * 
 * $Id$
 * 
 * @author Luis A. Floreani <luis.floreani@gmail.com>
 * @author gERD Schaufelberger <gerd@php-tools.net>
 * @package FIT
 * @subpackage Fixture
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 * @copyright Copyright (c) 2002-2005 Cunningham & Cunningham, Inc.
 */
 
/**
 * FIT Fixture
 * 
 * @version 0.1.0
 * @package FIT
 * @subpackage Fixture
 */
class eg_net_Simulator extends PHPFIT_Fixture_Action {

   /**
    * dictionary of variable types
    * @var int 
    */
    protected $typeDict = array(
                                'nodes' => 'integer',
                                'zip'   => 'integer',
                                'coord' => 'string',
                                'coord()' => 'integer',
                                'nodes()' => 'integer'
                            );
    

   /**
    * counts current nodes
    * @var int 
    */
    public $nodes = 0;
    
   /**
    * zip code
    * @var string 
    */
    public $zip;
    
   /**
    * geo coordinates
    * @var object 
    */
    public $coord;
    
   /**
    * new City
    * 
    * @return void
    */
    public function newCity() {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
    }
    
   /**
    * ok
    * 
    * @return void
    */
    public function ok() {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
        ++$this->nodes;
    }
   
   /**
    * nodes
    * 
    * @return void
    */
    public function cancel() {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
    }
    
   /**
    * name
    * 
    * @return void
    */
    public function name( $n ) {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
    }
    
   /**
    * zip
    * 
    * Update current zip or receive zip
    * 
    * @return void or current zip
    */
    public function zip( $z = null ) {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
        if( $z == null ) {
            return $this->zip;
        }
        
        $this->zip  =   $z;
    }
   
   /**
    * population
    * 
    * @return void
    */
    public function population( $p ) {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
    }
       
   /**
    * coord
    * 
    * @return void
    */
    public function coord( $c = null ) {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
        if( $c == null ) {
            return $this->geo;
        }
        $this->geo = $c;
    }
   
   /**
    * nodes
    * 
    * @return void
    */
    public function nodes() {
        //echo "net.Simulator->". __FUNCTION__ ."() \n";
        return $this->nodes;
    }
/*
    public Object parse (String string, Class type) throws Exception {
        if (type.equals(GeoCoordinate.class)) {return GeoCoordinate.parse(string);}
        return super.parse (string, type);
    }
*/
}