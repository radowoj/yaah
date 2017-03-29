<?php

namespace Radowoj\Yaah\HelperFactory;

use SoapClient;
use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Constants\Wsdl;

class Factory extends HelperFactory
{
    protected function getSoapClient()
    {
        return new SoapClient(
            $this->config->getIsSandbox()
                ? Wsdl::WSDL_SANDBOX
                : Wsdl::WSDL_PRODUCTION
        );
    }
}
