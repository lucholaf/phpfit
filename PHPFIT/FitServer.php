<?php

require_once 'PHPFIT/Fixture.php';
require_once 'PHPFIT/Socket.php';
require_once 'PHPFIT/HtmlRenderer/Fitnesse.php';

class PHPFIT_FitServer
{

    /**
    * @var PHPFIT_Counts
    */
    private $counts;
	private $totalCounts;

    /**
    * @var Socket
    */
    private $socket;

    const FITNESSE_INTEGER = 10;

    public function __construct($socket)
    {
        $this->socket = $socket;
    }


    /**
    * @param array $args
    */
    public function run($args)
    {
		if (in_array('-v', $args)) {
		    // Ignore verbose output for now
		    unset($args[array_search('-v', $args)]);
		    $args = array_values($args);
		}

        if (count($args) < 4 || count($args) > 5) {
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
			$this->totalCounts = new PHPFIT_Counts();

            $continue = true;
            while ($continue) {
            	$continue = $this->process($fixturePath);
            }
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
    public function init($host, $port, $ticket)
    {
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

	/**
	 * Returns true, if there might be more documents following.
	 *
	 * @param string $fixturePath
	 * @return boolean
	 */
    public function process($fixturePath)
    {
		$document = $this->getDocument();
		if (false === $document) {
		    return false;
		}
        $output = $this->processDocument($fixturePath, $document);
        $this->putDocument($output);
        $this->putSummary();
        return true;
    }

    public function finish()
    {
        $this->socket->close();
        return $this->totalCounts->wrong + $this->totalCounts->exceptions;
    }

    private function buildRequest($ticket) {
        return "GET /?responder=socketCatcher&ticket=" . $ticket . " HTTP/1.1\r\n\r\n";
    }

    /**
    * @param string $input
    * @return string
    */
    private function processDocument($fixturePath, $input)
    {
 		PHPFIT_Fixture::setHtmlRenderer(new PHPFIT_HtmlRenderer_Fitnesse());
        $fixture  = new PHPFIT_Fixture($fixturePath);
        try {
	        $tables = PHPFIT_Parse::create($input);
	        $fixture->doTables($tables);
        } catch (PHPFIT_Exception_Parse $e) {
            $tables = $this->exception($e, $fixture);
        }

        $this->counts = $fixture->counts;
        $this->totalCounts->tally($fixture->counts);
        return $tables->toString();
    }

	/**
	 * Returns false, if there is no more document on the socket.
	 * @return string|boolean
	 */
    private function getDocument()
    {
        $docSize = $this->socket->read(self::FITNESSE_INTEGER);
		if ($docSize == 0) {
		    return false;
		}
		$document = $this->socket->read($docSize);
        return $document;
    }

    /**
    * @param string $output
    */
    private function putDocument($output)
    {
        $this->putInteger(strlen($output));
        $this->socket->write($output, strlen($output));
    }

    private function putSummary()
    {
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
    private function putInteger($value)
    {
        $intValue = sprintf("%0" . self::FITNESSE_INTEGER . "d", $value);
        $this->socket->write($intValue, strlen($intValue));
    }

	/**
	 * @param Exception $e
	 */
    protected function exception(Exception $e, $fixture)
    {
        $message = "Exception occurred!";
        $tables = PHPFIT_Parse::createSimple("span", $message, null, null);
        $fixture->exception($tables, $e);
        $fixture->listener->tableFinished($tables);
        $fixture->listener->tablesFinished($fixture->counts);
        return $tables;
    }

}

