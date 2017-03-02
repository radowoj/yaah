<?php

namespace Radowoj\Yaah;

use SoapClient;

/**
 * When things go south on WebAPI Sandbox, it is good to be able to send XML of request/response to Allegro support.
 * This class helps with that. Just provide some path or php://stdout as $options['log_file'] and you're good to go.
 */
class DebugSoapClient extends SoapClient
{
    /**
     * Log file (or stream) to use for logging SOAP requests/responses
     * @var resource
     */
    protected $logFile = null;

    public function __construct($wsdl, array $options = [])
    {
        if (!array_key_exists('log_file', $options)) {
            throw new Exception("Debug soap client requires a log file path provided as {$options['log_file']} to work");
        }

        $this->logFile = fopen($options['log_file'], "w+");

        $options['trace'] = 1;

        parent::__construct($wsdl, $options);
    }


    public function __destruct()
    {
        fclose($this->logFile);
    }


    public function __call($functionName, $arguments)
    {
        $response = parent::__call($functionName, $arguments);

        $this->log("Request {$functionName}: \n{$this->__getLastRequest()}\n\n");
        $this->log("Response {$functionName}: \n{$this->__getLastResponse()}\n\n");

        return $response;
    }


    protected function log($message)
    {
        fwrite($this->logFile, $message);
    }
}
