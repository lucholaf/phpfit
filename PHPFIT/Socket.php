<?php
/**
* a socket wrapper
*/
class PHPFIT_Socket
{

    private $socket_resource;

    public function create($domain, $type, $protocol)
    {
        $this->socket_resource = socket_create($domain, $type, $protocol);
        if ($this->socket_resource < 0) {
            throw new Exception("socket_create() failed: " . $this->getLastSocketError() . "\n");
        }
    }

    public function connect($hostip, $port)
    {
        $result = socket_connect($this->socket_resource, $hostip, $port);
        if (false === $result) {
            throw new Exception("socket_connect() failed: " . $this->getLastSocketError() . "\n");
        }
    }

    public function read($len)
    {
		// First check, if there is any data available.
		// Without this check socket_read might hang and not even return "".
		// This happened with the FitServerTest JUnit tests, because
		// PHPFIT_FitServer::getDocument() tried to keep reading, even if there
		// was only one document and the TestServer did not close the socket.
		if (!$this->hasReadableData()) {
			throw new Exception('Socket::read() was called, but no readable data was available.');
		}
        return socket_read($this->socket_resource, $len);
    }

	public function hasReadableData()
	{
		$read = array($this->socket_resource);
		$write = null;
		$except = null;
		$result = socket_select($read, $write, $except, 1);
		if (false === $result) {
            throw new Exception("socket_select() before failed: " . $this->getLastSocketError() . "\n");
		}
		return $result > 0;
	}

    public function write($data, $len) {
        return socket_write($this->socket_resource, $data, $len);
    }

    public function close()
    {
        return socket_close($this->socket_resource);
    }

	protected function getLastSocketError()
	{
	    return socket_strerror(socket_last_error());
	}
}
