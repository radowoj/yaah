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

    protected function getPlainPasswordConfig()
    {
        $params = $this->defaultConfigParams;
        $params['passwordHash'] = '';

        $config = new Config($params);
        $config->setPasswordPlain('somePasswordPlain');
        return $config;
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


    public function testLoginFlow()
    {
        $config = $this->getPlainPasswordConfig();

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['doLogin', 'doQuerySysStatus', 'doSomethingAfterLogin'])
            ->getMock();

        //Allegro version key should be requested for first login per request
        $soapClient->expects($this->once())
            ->method('doQuerySysStatus')
            ->willReturn((object)['verKey' => 'someVersionKey']);

        //login
        $soapClient->expects($this->once())
            ->method('doLogin')
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


    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Invalid WebAPI doLogin[Enc]() response
     */
    public function testExceptionOnInvalidLoginResponseMissingUserId()
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
            ->willReturn((object)['sessionHandlePart' => 'foo']);

        $client = new Client($config, $soapClient);
    }


    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Invalid WebAPI doLogin[Enc]() response
     */
    public function testExceptionOnInvalidLoginResponseMissingSessionHandlePart()
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
            ->willReturn((object)['userId' => 'bar']);

        $client = new Client($config, $soapClient);
    }


    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Invalid WebAPI doQuerySysStatus() response
     */
    public function testExceptionOnInvalidQuerySysStatusResponse()
    {
        $config = $this->getConfig();

        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['doQuerySysStatus', 'doSomethingAfterLogin'])
            ->getMock();

        //Allegro version key should be requested for first login per request
        $soapClient->expects($this->once())
            ->method('doQuerySysStatus')
            ->willReturn((object)['trololo' => 'thisIsNotAVersionKey']);

        $client = new Client($config, $soapClient);
    }



}
