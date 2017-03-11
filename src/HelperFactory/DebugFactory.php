<?php

namespace Radowoj\Yaah\HelperFactory;

use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Constants\Wsdl;
use Radowoj\Yaah\DebugSoapClient;

class DebugFactory extends HelperFactory
{
    public function getSoapClient()
    {
        return new DebugSoapClient(
            $this->getConfig()->getIsSandbox()
                ? Wsdl::WSDL_SANDBOX
                : Wsdl::WSDL_PRODUCTION,
            ['log_file' => 'php://stdout']
        );
    }
}
