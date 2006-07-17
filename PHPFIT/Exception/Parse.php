<?PHP

class PHPFIT_Exception_Parse extends Exception {
    
    /**
    * Exception string offset of parser
    * @var string
    */
    protected $offset = 0;
    
    /**
    * constructor
    * 
    * @param string $msg exception message
    * @param string $offset
    * @see Exception
    */
    public function __construct( $msg, $offset ) {
        $this->offset = $offset;
        $this->message = $msg;
        parent::__construct($this->message);
    }   
    
    /**
    * receive offset
    * @return int parser offset
    */
    public function getOffset() {
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