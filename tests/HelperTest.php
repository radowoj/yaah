<?php

namespace Radowoj\Yaah;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Field;
use SoapClient;

class HelperTest extends TestCase
{
    protected $config = null;

    protected $soapClient = null;

    public function setUp()
    {
        $this->config = $this->getMockBuilder(Config::class)
            ->setMethods(['getApiKey'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->config->expects($this->any())
            ->method('getApiKey')
            ->willReturn('someApiKey');

        $this->soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['doNewAuctionExt', 'doVerifyItem', 'doQuerySysStatus', 'doLogin', 'doSomethingNotImplementedInHelper'])
            ->getMock();
    }


    public function testNewAuction()
    {
        $apiClient = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->config, $this->soapClient])
            ->setMethods(['doNewAuctionExt', 'doVerifyItem'])
            ->getMock();

        $apiClient->expects($this->once())
            ->method('doNewAuctionExt')
            ->with($this->equalTo([
                'fields' => [
                    [
                        'fvalueString' => 'test title',
                        'fid' => 1,
                        'fvalueInt' => 0,
                        'fvalueFloat' => 0,
                        'fvalueImage' => '',
                        'fvalueDate' => '',
                        'fvalueDatetime' => 0,
                        'fvalueRangeInt' => [
                            'fvalueRangeIntMin' => 0,
                            'fvalueRangeIntMax' => 0,
                        ],
                        'fvalueRangeFloat' => [
                            'fvalueRangeFloatMin' => 0,
                            'fvalueRangeFloatMax' => 0,
                        ],
                        'fvalueRangeDate' => [
                            'fvalueRangeDateMin' => '',
                            'fvalueRangeDateMax' => '',
                        ]
                    ]
                ],
                'localId' => 1
            ]));

        $apiClient->expects($this->once())
            ->method('doVerifyItem')
            ->willReturn((object)['itemId' => 1234]);

        $helper = new Helper($apiClient);

        $result = $helper->newAuction(new Auction([1 => 'test title']), 1);

        $this->assertSame(1234, $result);
    }

    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Auction has not been created
     */
    public function testExceptionOnInvalidNewAuctionResponse()
    {
        $apiClient = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->config, $this->soapClient])
            ->setMethods(['doNewAuctionExt', 'doVerifyItem'])
            ->getMock();

        $apiClient->expects($this->once())
            ->method('doNewAuctionExt')
            ->willReturn((object)['whatever' => 1]);

        $apiClient->expects($this->once())
            ->method('doVerifyItem')
            ->willReturn((object)['definitelyNotItemId' => 1234]);

        $helper = new Helper($apiClient);
        $helper->newAuction(new Auction([1 => 'test title']), 1);
    }


    public function testDirectWebapiCall()
    {
        $apiClient = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->config, $this->soapClient])
            ->setMethods(['doSomethingNotImplementedInHelper'])
            ->getMock();

        $apiClient->expects($this->once())
            ->method('doSomethingNotImplementedInHelper')
            ->with($this->equalTo([
                'param1' => 1,
                'param2' => 2,
            ]))
            ->willReturn(42);

        $helper = new Helper($apiClient);
        $result = $helper->doSomethingNotImplementedInHelper([
            'param1' => 1,
            'param2' => 2,
        ]);

        $this->assertSame(42, $result);
    }


    /**
     * This test checks if call of a method not implemented in helper is correctly passed via WebAPI client to soapClient
     * (extended by WebAPI request param - just one is tested here, as all required params are tested in ClientTest)
     */
    public function testDirectWebapiCallFromSoapClient()
    {
        $this->soapClient->expects($this->once())
            ->method('doQuerySysStatus')
            ->willReturn((object)['verKey' => 'someVersionKey']);

        $this->soapClient->expects($this->once())
            ->method('doLogin')
            ->willReturn((object)['sessionHandlePart' => 'foo', 'userId' => 'bar']);

        $this->soapClient->expects($this->once())
            ->method('doSomethingNotImplementedInHelper')
            ->with(
                $this->callback(function($params){
                    return array_key_exists('param1', $params)
                        && array_key_exists('webapiKey', $params)
                        && $params['param1'] == 1337
                        && $params['webapiKey'] == 'someApiKey';
                })
            )
            ->willReturn(42);

        $apiClient = new Client($this->config, $this->soapClient);

        $helper = new Helper($apiClient);
        $result = $helper->doSomethingNotImplementedInHelper([
            'param1' => 1337
        ]);

        $this->assertSame(42, $result);
    }


}
