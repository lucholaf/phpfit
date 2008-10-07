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
            throw new Exception("socket_create() failed: " . socket_strerror($this->socket_resource) . "\n");
        }
    }

    public function connect($hostip, $port)
    {
        $result = socket_connect($this->socket_resource, $hostip, $port);
        if ($result < 0) {
            throw new Exception("socket_connect() failed: " . socket_strerror($this->socket_resource) . "\n");
        }
    }

    public function read($len)
    {
        return socket_read($this->socket_resource, $len);
    }

    public function write($data, $len) {
        return socket_write($this->socket_resource, $data, $len);
    }

    public function close()
    {
        return socket_close($this->socket_resource);
    }
}
