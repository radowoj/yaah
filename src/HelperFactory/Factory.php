<?php

namespace Radowoj\Yaah\HelperFactory;

use SoapClient;
use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Constants\Wsdl;

class Factory extends HelperFactory
{
    public function getSoapClient()
    {
        return new SoapClient(
            $this->getConfig()->getIsSandbox()
                ? Wsdl::WSDL_SANDBOX
                : Wsdl::WSDL_PRODUCTION
        );
    }
}
