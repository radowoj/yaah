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

    public function create(array $configArray)
    {
        $this->config = new Config($configArray);
        $soapClient = $this->getSoapClient($this->config);
        $apiClient = new Client($this->config, $soapClient);
        return new Helper($apiClient);
    }

}
