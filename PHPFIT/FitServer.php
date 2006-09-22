<?php

/*
http://fitnesse.org/FitNesse.FitServerProtocol

put this in your wiki pages:
!define COMMAND_PATTERN {php /path/to/PHPFIT/FitServer.php}
*/

error_reporting(E_ALL);

require_once 'PHPFIT/Fixture.php';


/**
* a socket wrapper
*/
class Socket {
    
    private $socket_resource;
    
    public function create($domain, $type, $protocol) {
        $this->socket_resource = socket_create($domain, $type, $protocol);
        if ($this->socket_resource < 0) {
            throw new Exception("socket_create() failed: " . socket_strerror($this->socket_resource) . "\n");
        }
    }
    
    public function connect($hostip, $port) {
        $result = socket_connect($this->socket_resource, $hostip, $port);
        if ($result < 0) {
            throw new Exception("socket_connect() failed: " . socket_strerror($this->socket_resource) . "\n");
        }
    }
    
    public function read($len) {
        return socket_read($this->socket_resource, $len);
    }
    
    public function write($data, $len) {
        return socket_write($this->socket_resource, $data, $len);
    }
    
    public function close() {
        return socket_close($this->socket_resource);
    }
}

class FitServer {
    
    /**
    * @var PHPFIT_Counts
    */
    private $counts;
    
    /**
    * @var Socket
    */
    private $socket;
    
    const FITNESSE_INTEGER = 10;
    
    public function __construct($socket) {
        $this->socket = $socket;
    }
    
    
    /**
    * @param array $args
    */
    public function run($args) {
        if (count($args) != 4) {
            echo "Usage: php FitServer.php <host> <port> <test_ticket>";
            return -1;
        }

        try {
            $this->init($args[1], $args[2], $args[3]);
            $this->process();
            $out = $this->finish();
        } catch (Exception $e) {
            echo $e->getMessage();
            $out = -1;
        }
        
        return $out;
    }
    
    
    /**
    * @param string $host
    * @param string $port
    * @param string $ticket
    */
    public function init($host, $port, $ticket) {
        
        $httpRequest = $this->buildRequest($ticket); 
        
        $this->socket->create(AF_INET, SOCK_STREAM, SOL_TCP);            

        $this->socket->connect(gethostbyname($host), $port);
        
        $this->socket->write($httpRequest, strlen($httpRequest));
        
        if ($this->socket->read(self::FITNESSE_INTEGER) != 0) {
            $this->socket->close(); 
            throw new Exception("init() failed: " . socket_strerror($this->socket) . "\n");
        }
    }    
    
    public function process() {        
        $output = $this->processDocument($this->getDocument());
        $this->putDocument($output);
        $this->putSummary();                
    }
    
    public function finish() {
        $this->socket->read(self::FITNESSE_INTEGER); // should read 0        
        $this->socket->close();       
        return $this->counts->wrong + $this->counts->exceptions;        
    }
    
    private function buildRequest($ticket) {
        return "GET /?responder=socketCatcher&ticket=" . $ticket . " HTTP/1.1\r\n\r\n";
    }
    
    /**
    * @param string $input
    * @return string
    */
    private function processDocument($input) {
        $fixture = new PHPFIT_Fixture();
        $fixture->doInput($input);
        $this->counts = $fixture->counts;
        return $fixture->toString();
    }
    
    private function getDocument() {
        $docSize = $this->socket->read(self::FITNESSE_INTEGER);
        return $this->socket->read($docSize);
    }

    /**
    * @param string $output
    */    
    private function putDocument($output) {
        $this->putInteger(strlen($output));        
        $this->socket->write($output, strlen($output));
    }
    
    private function putSummary() {
        $this->putInteger(0);
        $this->putInteger($this->counts->right);
        $this->putInteger($this->counts->wrong);
        $this->putInteger($this->counts->ignores);
        $this->putInteger($this->counts->exceptions);
    }

    /**
    * sends a 10 bytes value
    *
    * @param integer $value
    */     
    private function putInteger($value) {
        $intValue = sprintf("%0" . self::FITNESSE_INTEGER . "d", $value);
        $this->socket->write($intValue, strlen($intValue));        
    }
}

/* If this is the script executed, run the fitserver */
if ($_SERVER['SCRIPT_NAME'] == __FILE__ ) {
    $fitserver = new FitServer(new Socket());
    $out = $fitserver->run($argv);
    exit($out);
}

?>
