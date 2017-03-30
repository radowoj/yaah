<?php

namespace Radowoj\Yaah\Journal;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Auction;

class DealTest extends TestCase
{
    protected function getExampleDeal()
    {
        return (object)[
            'dealEventId' => 1234,
            'dealEventType' => 1,
            'dealEventTime' => time(),
            'dealId' => 42,
            'dealTransactionId' => 4321,
            'dealSellerId' => 12345,
            'dealItemId' => 1234512345,
            'dealBuyerId' => 54321,
            'dealQuantity' => 2,
        ];
    }


    public function testMapFromObject()
    {
        $exampleDeal = $this->getExampleDeal();
        $deal = new Deal($exampleDeal);

        $this->assertSame($exampleDeal->dealEventId, $deal->getEventId());
        $this->assertSame($exampleDeal->dealEventType, $deal->getEventType());
        $this->assertSame($exampleDeal->dealId, $deal->getId());
        $this->assertSame($exampleDeal->dealTransactionId, $deal->getTransactionId());
        $this->assertSame($exampleDeal->dealSellerId, $deal->getSellerId());
        $this->assertSame($exampleDeal->dealItemId, $deal->getItemId());
        $this->assertSame($exampleDeal->dealBuyerId, $deal->getBuyerId());
        $this->assertSame($exampleDeal->dealQuantity, $deal->getQuantity());
    }


    public function testDealTimeFormatting()
    {
        $eventTime = time();
        $deal = new Deal((object)['dealEventTime' => $eventTime]);

        $this->assertSame(
            date('d.m.Y', $eventTime),
            $deal->getEventTime('d.m.Y'),
            'Custom formatting from argument'
        );

        $this->assertSame(
            date('Y-m-d H:i:s', $eventTime),
            $deal->getEventTime(),
            'Default formatting'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Original deal property name must start with "deal"
     */
    public function testExceptionOnNonDealProperty()
    {
        $deal = new Deal((object)['romanes' => 'eunt domus']);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown Deal property: er
     */
    public function testExceptionOnInvalidDealProperty()
    {
        $deal = new Deal((object)['dealer' => 'Pinkman']);
    }


    public function testTypeDetection()
    {
        $deal = new Deal((object)['dealEventType' => 1]);
        $this->assertTrue($deal->isTypeCreateDeal());
        $this->assertFalse($deal->isTypeCreatePostSaleForm());
        $this->assertFalse($deal->isTypeAbortPostSaleForm());
        $this->assertFalse($deal->isTypeFinishDeal());

        $deal = new Deal((object)['dealEventType' => 2]);
        $this->assertFalse($deal->isTypeCreateDeal());
        $this->assertTrue($deal->isTypeCreatePostSaleForm());
        $this->assertFalse($deal->isTypeAbortPostSaleForm());
        $this->assertFalse($deal->isTypeFinishDeal());

        $deal = new Deal((object)['dealEventType' => 3]);
        $this->assertFalse($deal->isTypeCreateDeal());
        $this->assertFalse($deal->isTypeCreatePostSaleForm());
        $this->assertTrue($deal->isTypeAbortPostSaleForm());
        $this->assertFalse($deal->isTypeFinishDeal());

        $deal = new Deal((object)['dealEventType' => 4]);
        $this->assertFalse($deal->isTypeCreateDeal());
        $this->assertFalse($deal->isTypeCreatePostSaleForm());
        $this->assertFalse($deal->isTypeAbortPostSaleForm());
        $this->assertTrue($deal->isTypeFinishDeal());
    }

}
