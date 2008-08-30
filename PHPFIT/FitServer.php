<?php

require_once 'PHPFIT/Fixture.php';

/**
* a socket wrapper
*/
class PHPFIT_Socket {

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

class PHPFIT_FitServer {

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
        if (count($args) < 4) {
            echo "Usage: php FitServer.php <host> <port> <test_ticket>";
            return -1;
        }

        $fixturePath = null;
        if (count($args) == 5) {
            $fixturePath = $args[1];
            array_shift($args);
        }

        array_shift($args);

        try {
            $this->init($args[0], $args[1], $args[2]);
            $this->process($fixturePath);
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

        if (($status = $this->socket->read(self::FITNESSE_INTEGER)) != 0) {
            $errorMsg = $this->socket->read($status);
            $this->socket->close();
            throw new Exception("init() failed: " . $errorMsg . "\n");
        }
    }

    public function process($fixturePath) {
        $output = $this->processDocument($fixturePath, $this->getDocument());
        $this->putDocument($output);
        $this->putSummary();
    }

    public function finish() {
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
    private function processDocument($fixturePath, $input) {
        $fixture  = new PHPFIT_Fixture($fixturePath);
        $tables = PHPFIT_Parse::create($input);
        $fixture->doTables($tables);

        $this->counts = $fixture->counts;
        return $tables->toString();
    }

    private function getDocument() {
        $document = "";
        while (($docSize = $this->socket->read(self::FITNESSE_INTEGER)) != 0) {
            $document .= $this->socket->read($docSize);
        }
        return $document;
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

?>
