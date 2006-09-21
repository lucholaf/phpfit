<?php

/* 
TODO: ACCOMPLISH THE WHOLE FITNESSE PROTOCOL SPECIFICATION.
http://fitnesse.org/FitNesse.FitServerProtocol
*/

error_reporting( E_ALL );

require_once 'PHPFIT/Fixture.php';

class FitServer {
    
    public static function run($args) {
        $socket = self::connect($args[1], $args[2], $args[3]);
        self::process($socket);
        self::finish($socket);
    }
    
    public static function connect($host, $port, $ticket) {
        
        $hostip = gethostbyname($host);
        
        $httpRequest = "GET /?responder=socketCatcher&ticket=" . $ticket . " HTTP/1.1\r\n\r\n";
        
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket < 0) {
            echo "socket_create() failed: " . socket_strerror($socket) . "\n";
            die();
        }
        
        $result = socket_connect($socket, $hostip, $port);
        if ($result < 0) {
            echo "socket_connect() failed: ($result) " .
            socket_strerror($result) . "\n";
            socket_close($socket);
            die();
        }
        
        if (!socket_write($socket, $httpRequest, strlen($httpRequest))) {
            echo "status error while writing";
            socket_close($socket);
            die();
        }
        
        if (!socket_read($socket, 10, PHP_NORMAL_READ)) {
            echo "status error while reading";
            socket_close($socket);
            die();
        }
        
        return $socket;
        
    }
    
    public static function process($socket) {
        $status = socket_read($socket, 10);
        
        $input = socket_read($socket, $status);
        
        $fixture = new PHPFIT_Fixture();
        $fixture->doInput($input);
        $output = $fixture->toString();
        
        self::printFitNesseInteger($socket, strlen($output));
        
        socket_write($socket, $output, strlen($output));
        
        self::printFitNesseInteger($socket, 0);
        
        self::printFitNesseInteger($socket, $fixture->counts->right);
        self::printFitNesseInteger($socket, $fixture->counts->wrong);
        self::printFitNesseInteger($socket, $fixture->counts->ignores);
        self::printFitNesseInteger($socket, $fixture->counts->exceptions);
        
        $status = socket_read($socket, 10);        
            
    }
    
    public static function finish($socket) {
        socket_close($socket);
    }
    
    public static function printFitNesseInteger($socket, $value) {
        $intValue = sprintf("%010d", $value);
        socket_write($socket, $intValue, strlen($intValue));        
    }
    
}

FitServer::run($argv);

?>
