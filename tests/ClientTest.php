<?php

namespace Radowoj\Yaah;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;

use SoapClient;

class ClientTest extends TestCase
{

    protected $defaultConfigParams = [
        'apiKey' => 'some api key',
        'login' => 'someLogin',
        'passwordHash' => 'passwordHash',
        'isSandbox' => true,
        'countryCode' => 1,
    ];


    protected function getConfig()
    {
        return new Config($this->defaultConfigParams);
    }

    public function testLoginEncFlow()
    {
        $config = $this->getConfig();

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['doLoginEnc', 'doQuerySysStatus', 'doSomethingAfterLogin'])
            ->getMock();

        //Allegro version key should be requested for first login per request
        $soapClient->expects($this->once())
            ->method('doQuerySysStatus')
            ->willReturn((object)['verKey' => 'someVersionKey']);

        //login
        $soapClient->expects($this->once())
            ->method('doLoginEnc')
            ->willReturn((object)['sessionHandlePart' => 'foo', 'userId' => 'bar']);

        $client = new Client($config, $soapClient);

        //check session handle
        $soapClient->expects($this->once())
            ->method('doSomethingAfterLogin')
            ->with($this->equalTo([
                'webapiKey' => $this->defaultConfigParams['apiKey'],
                'localVersion' => 'someVersionKey',
                'countryId' => $this->defaultConfigParams['countryCode'],
                'sessionId' => 'foo',
                'countryCode' => $this->defaultConfigParams['countryCode'],
                'sessionHandle' => 'foo',
                'someFakeRequestParam' => 'someFakeRequestValue'
            ]));

        $client->somethingAfterLogin(['someFakeRequestParam' => 'someFakeRequestValue']);

    }
}
