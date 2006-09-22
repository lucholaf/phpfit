<?php

require_once 'PHPFIT/FitServer.php';

class FitServerTest extends UnitTestCase {
    public function testConnect() {
        $mock = new MockSocket();
        $fitserver = new FitServer($mock);        
        
        $fitserver->connect("localhost", "80", 101);

        $this->assertEqual(true, $mock->created);
        $this->assertEqual(true, $mock->connected);
        $this->assertEqual("GET /?responder=socketCatcher&ticket=101 HTTP/1.1\r\n\r\n", $mock->written);
        $this->assertEqual(10, $mock->read_len);
    }
    
    public function testTransaction() {
    }

    public function testClose() {
    }
}

class MockSocket {
    public $created = false;
    public $connected = false;
    public $written;
    public $read_len;
    
    public function create($domain, $type, $protocol) {
        $this->created = true;
        return 0;
    }
    
    public function connect($socket, $hostip, $port) {
        $this->connected = true;
        return 0;
    }
    
    public function read($socket, $len) {
        $this->read_len = $len;
        return 1;
    }
    
    public function write($socket, $data, $len) {
        $this->written = $data;
        return 1;
    }
    
}

?>