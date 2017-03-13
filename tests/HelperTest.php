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
            ->disableOriginalConstructor()
            ->getMock();

        $this->soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['doQuerySysStatus', 'doLogin', 'doNewAuctionExt', 'doVerifyItem'])
            ->getMock();

        $this->soapClient->expects($this->once())
            ->method('doQuerySysStatus')
            ->willReturn((object)['verKey' => 'someVersionKey']);

        $this->soapClient->expects($this->once())
            ->method('doLogin')
            ->willReturn((object)['userId' => 'someUserId', 'sessionHandlePart' => 'someSessionHandlePart']);

    }


    public function testNewAuction()
    {
        $apiClient = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->config, $this->soapClient])
            ->setMethods(['newAuctionExt', 'verifyItem'])
            ->getMock();

        $apiClient->expects($this->once())
            ->method('newAuctionExt')
            ->with([
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
            ]);

        $apiClient->expects($this->once())
            ->method('verifyItem')
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
            ->setMethods(['newAuctionExt', 'verifyItem'])
            ->getMock();

        $apiClient->expects($this->once())
            ->method('newAuctionExt')
            ->willReturn((object)['whatever' => 1]);

        $apiClient->expects($this->once())
            ->method('verifyItem')
            ->willReturn((object)['definitelyNotItemId' => 1234]);

        $helper = new Helper($apiClient);
        $helper->newAuction(new Auction([1 => 'test title']), 1);
    }
}
