<?php

/*
TODO: ACCOMPLISH THE WHOLE FITNESSE PROTOCOL SPECIFICATION.
http://fitnesse.org/FitNesse.FitServerProtocol
*/

error_reporting(E_ALL);

require_once 'PHPFIT/Fixture.php';

class Socket {
    
    private $socket;
    
    public function create($domain, $type, $protocol) {
        $this->socket = socket_create($domain, $type, $protocol);
        if ($this->socket < 0) {
            echo "socket_create() failed: " . socket_strerror($this->socket) . "\n";
            die();
        }
    }
    
    public function connect($hostip, $port) {
        $result = socket_connect($this->socket, $hostip, $port);
        if ($result < 0) {
            echo "socket_connect() failed: ($result) " .
            socket_strerror($result) . "\n";
            $this->close();
            die();
        }
    }
    
    public function read($len) {
        return socket_read($this->socket, $len);
    }
    
    public function write($data, $len) {
        return socket_write($this->socket, $data, $len);
    }
    
    public function close() {
        return socket_close($this->socket);
    }
}

class FitServer {
    
    private $fixture;
    private $socketObject;
    
    const FITNESSE_INTEGER = 10;
    
    public function __construct($socketObject) {
        $this->socketObject = $socketObject;
    }
    
    public function run($args) {
        $socket = $this->connect($args[1], $args[2], $args[3]);
        $this->process($socket);
        return $this->close();
    }
    
    public function connect($host, $port, $ticket) {
        
        $hostip = gethostbyname($host);
        
        $httpRequest = "GET /?responder=socketCatcher&ticket=" . $ticket . " HTTP/1.1\r\n\r\n";
        
        $this->socketObject->create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        $this->socketObject->connect($hostip, $port);
        
        $this->socketObject->write($httpRequest, strlen($httpRequest));
        
        $this->socketObject->read(self::FITNESSE_INTEGER);
    }
    
    
    public function process($socket) {
        
        $output = $this->processDocument($this->getDocument($socket));
        
        $this->putDocument($socket, $output);
        
        $this->putSummary($socket);        
        
        $status = $this->socketObject->read(self::FITNESSE_INTEGER);
        
    }
    
    public function close() {
        $this->socketObject->close();
        
        return $this->fixture->counts->wrong + $this->fixture->counts->exceptions;        
    }


    
    private function processDocument($input) {
        $this->fixture = new PHPFIT_Fixture();
        $this->fixture->doInput($input);
        return $this->fixture->toString();
    }
    
    private function getDocument($socket) {
        $msgLen = $this->socketObject->read(self::FITNESSE_INTEGER);        
        return $this->socketObject->read($msgLen);
    }
    
    private function putDocument($socket, $output) {
        $this->putInteger($socket, strlen($output));        
        $this->socketObject->write($output, strlen($output));
        $this->putInteger($socket, 0);
    }
    
    private function putSummary($socket) {
        $this->putInteger($socket, $this->fixture->counts->right);
        $this->putInteger($socket, $this->fixture->counts->wrong);
        $this->putInteger($socket, $this->fixture->counts->ignores);
        $this->putInteger($socket, $this->fixture->counts->exceptions);
    }
    
    private function putInteger($socket, $value) {
        $intValue = sprintf("%0" . self::FITNESSE_INTEGER . "d", $value);
        $this->socketObject->write($intValue, strlen($intValue));        
    }
}

if ($_SERVER['SCRIPT_NAME'] == __FILE__ ) {
    $fitserver = new FitServer(new Socket());
    $out = $fitserver->run($argv);
    exit($out);
}

?>
