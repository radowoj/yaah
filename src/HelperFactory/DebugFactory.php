<?php

namespace Radowoj\Yaah\HelperFactory;

use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Constants\Wsdl;
use Radowoj\Yaah\DebugSoapClient;

class DebugFactory extends HelperFactory
{
    protected function getSoapClient()
    {
        return new DebugSoapClient(
            $this->config->getIsSandbox()
                ? Wsdl::WSDL_SANDBOX
                : Wsdl::WSDL_PRODUCTION,
            ['log_file' => 'php://stdout']
        );
    }
}
