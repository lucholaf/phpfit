<?php
/**
* a socket wrapper
*/
class PHPFIT_Socket
{
    const CHUNK_SIZE = 10;

	private $connectRetries = 3;
    private $readRetries = 20;
	private $usleepTime = 2000;

    private $socketResource;

    public function create($domain, $type, $protocol)
    {
        $this->socketResource = socket_create($domain, $type, $protocol);
        if ($this->socketResource < 0) {
            throw new Exception("socket_create() failed: " . $this->getLastSocketError() . "\n");
        }
    }

    public function connect($hostip, $port)
    {
    	$result = @socket_connect($this->socketResource, $hostip, $port);
		$tries = 0;
		// Allow for some retries. This makes the FitServerTest a little more stable.
		while ($result === false && $tries < $this->connectRetries) {
        	usleep($this->usleepTime);
        	$result = @socket_connect($this->socketResource, $hostip, $port);
        	$tries++;
		}
        if (false === $result) {
            throw new Exception("socket_connect() failed: " . $this->getLastSocketError() . "\n");
        }
    }

    public function read($len)
    {
		$this->ensureReadableData();
        return $this->readInChunks($len);
    }

    private function readInChunks($len)
    {
        $message  = '';
        while ($len > self::CHUNK_SIZE) {
            $len -= self::CHUNK_SIZE;
            $message .= socket_read($this->socketResource, self::CHUNK_SIZE);
        }
        return $message . socket_read($this->socketResource, $len);
    }

    private function ensureReadableData()
    {
        // Without this check socket_read might hang and not even return "".
        // This happened with the FitServerTest JUnit tests, because
        // PHPFIT_FitServer::getDocument() tried to keep reading, even if there
        // was only one document and the TestServer did not close the socket.
        $i = 0;
        while(!$this->hasReadableData()) {
            if ($i > $this->_numberOfRetries) {
                $this->raiseError(
                    "Socket::read() was called, " .
                    "but no readable data was available."
                );
            }
            usleep(10000);
            $i++;
        }
    }

	public function hasReadableData()
	{
		$read = array($this->socketResource);
		$write = null;
		$except = null;
		$result = socket_select($read, $write, $except, 1);
		if (false === $result) {
            throw new Exception("socket_select() before failed: " . $this->getLastSocketError() . "\n");
		}
		return $result > 0;
	}

    public function write($data, $len)
    {
        return socket_write($this->socketResource, $data, $len);
    }

    public function close()
    {
        return socket_close($this->socketResource);
    }

	protected function getLastSocketError()
	{
	    return socket_strerror(socket_last_error());
	}
}
