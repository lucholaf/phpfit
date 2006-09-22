<?php

/*
TODO: ACCOMPLISH THE WHOLE FITNESSE PROTOCOL SPECIFICATION.
http://fitnesse.org/FitNesse.FitServerProtocol
*/

error_reporting(E_ALL);

require_once 'PHPFIT/Fixture.php';

class Socket {
    public function create($domain, $type, $protocol) {
        return socket_create($domain, $type, $protocol);
    }
    
    public function connect($socket, $hostip, $port) {
        return socket_connect($socket, $hostip, $port);
    }
    
    public function read($socket, $len) {
        return socket_read($socket, $len);
    }
    
    public function write($socket, $data, $len) {
        return socket_write($socket, $data, $len);
    }
}

class FitServer {
    
    public $socketObject;
    
    public function __construct($socketObject) {
        $this->socketObject = $socketObject;
    }
    
    public function run($args) {
        $socket = $this->connect($args[1], $args[2], $args[3]);
        return $this->process($socket);
    }
    
    public function connect($host, $port, $ticket) {
        
        $hostip = gethostbyname($host);
        
        $httpRequest = "GET /?responder=socketCatcher&ticket=" . $ticket . " HTTP/1.1\r\n\r\n";
        
        $socket = $this->socketObject->create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if ($socket < 0) {
            echo "socket_create() failed: " . socket_strerror($socket) . "\n";
            die();
        }
        
        $result = $this->socketObject->connect($socket, $hostip, $port);
        if ($result < 0) {
            echo "socket_connect() failed: ($result) " .
            socket_strerror($result) . "\n";
            socket_close($socket);
            die();
        }
        
        if (!$this->socketObject->write($socket, $httpRequest, strlen($httpRequest))) {
            echo "status error while writing";
            socket_close($socket);
            die();
        }
        
        if (!$this->socketObject->read($socket, 10)) {
            echo "status error while reading";
            socket_close($socket);
            die();
        }
        
        return $socket;
        
    }
    
    public function process($socket) {
        $msgLen = $this->socketObject->read($socket, 10);
        
        $input = $this->socketObject->read($socket, $msgLen);
        
        $fixture = new PHPFIT_Fixture();
        $fixture->doInput($input);
        $output = $fixture->toString();
        
        $this->printFitNesseInteger($socket, strlen($output));
        
        $this->socketObject->write($socket, $output, strlen($output));
        
        $this->printFitNesseInteger($socket, 0);
        
        $this->printFitNesseInteger($socket, $fixture->counts->right);
        $this->printFitNesseInteger($socket, $fixture->counts->wrong);
        $this->printFitNesseInteger($socket, $fixture->counts->ignores);
        $this->printFitNesseInteger($socket, $fixture->counts->exceptions);
        
        $status = $this->socketObject->read($socket, 10);
        
        socket_close($socket);
        
        return $fixture->counts->wrong + $fixture->counts->exceptions;
            
    }
    
    public function printFitNesseInteger($socket, $value) {
        $intValue = sprintf("%010d", $value);
        $this->socketObject->write($socket, $intValue, strlen($intValue));        
    }
}

if ($_SERVER['SCRIPT_NAME'] == __FILE__ ) {
    $fitserver = new FitServer(new Socket());
    $out = $fitserver->run($argv);
    exit($out);
}

?>
