<?php

namespace Radowoj\Yaah\HelperFactory;

use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Helper;
use Radowoj\Yaah\Exception;

abstract class HelperFactory
{
    protected $config = null;

    abstract protected function getSoapClient();

    protected function setConfig($configArray)
    {
        $this->config = new Config($configArray);
    }

    protected function getConfig()
    {
        if (is_null($this->config)) {
            throw new Exception('Config object has not been set');
        }

        return $this->config;
    }

    public function create(array $configArray)
    {
        $this->setConfig($configArray);
        $soapClient = $this->getSoapClient($this->getConfig());
        $apiClient = new Client($this->getConfig(), $soapClient);
        return new Helper($apiClient);
    }

}
