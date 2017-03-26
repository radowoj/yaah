<?php

namespace Radowoj\Yaah\Decorators;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Auction;
use Radowoj\Yaah\Decorators\AuctionArrayMapDecorator;
use Radowoj\Yaah\Constants\AuctionTimespans;
use Radowoj\Yaah\Constants\Conditions;
use Radowoj\Yaah\Constants\SaleFormats;
use Radowoj\Yaah\Constants\ShippingPaidBy;

class AuctionArrayMapDecoratorTest extends TestCase
{


    protected function getTestPhotoArray()
    {
        return [
            __DIR__ . '/i.am.a.photo.file.txt'
        ];
    }


    protected function getTestArray()
    {
        return [
            'title' => 'Allegro test auction',
            'description' => 'Test auction description',
            'category' => 6092,
            'timespan' => AuctionTimespans::TIMESPAN_3_DAYS,
            'quantity' => 100,
            'country' => 1,
            'region' => 15,
            'city' => 'SomeCity',
            'postcode' => '12-345',
            'condition' => Conditions::CONDITION_NEW,
            'sale_format' => SaleFormats::SALE_FORMAT_SHOP,
            'buy_now_price' => 43,
            'shipping_paid_by' => ShippingPaidBy::SHIPPING_PAID_BY_BUYER,
            'post_package_priority_price' => 12,
        ];
    }


    protected function getTestFidArray()
    {
        return [
            1 => 'Allegro test auction',
            24 => 'Test auction description',
            2 => 6092,
            4 => 0,
            5 => 100,
            9 => 1,
            10 => 15,
            11 => 'SomeCity',
            32 => '12-345',
            20626 => 1,
            29 => 1,
            8 => 43,
            12 => 1,
            38 => 12,
        ];
    }


    protected function getTestMap()
    {
        return [
            'title' => 1,
            'description' => 24,
            'category' => 2,
            'timespan' => 4,
            'quantity' => 5,
            'country' => 9,
            'region' => 10,
            'city' => 11,
            'postcode' => 32,
            'condition' => 20626,
            'sale_format' => 29,
            'buy_now_price' => 8,
            'shipping_paid_by' => 12,
            'post_package_priority_price' => 38,
        ];
    }


    protected function getDecorator(Auction $auction)
    {
        $decorator = $this->getMockForAbstractClass(AuctionArrayMapDecorator::class, [
            $auction
        ]);

        $decorator->expects($this->any())
            ->method('getMap')
            ->willReturn(
                $this->getTestMap()
            );

        return $decorator;
    }


    public function testFromArray()
    {
        $auction = $this->getMockBuilder(Auction::class)
            ->setMethods(['fromArray'])
            ->getMock();

        $auction->expects($this->once())
            ->method('fromArray')
            ->with($this->getTestFidArray())
            ->willReturn(null);

        $decorator = $this->getDecorator($auction);

        $decorator->fromArray($this->getTestArray());
    }


    public function testToArray()
    {
        $auction = $this->getMockBuilder(Auction::class)
            ->setMethods(['toArray'])
            ->getMock();

        $auction->expects($this->once())
            ->method('toArray')
            ->willReturn($this->getTestFidArray());

        $decorator = $this->getDecorator($auction);

        $this->assertSame($this->getTestArray(), $decorator->toArray());
    }



    public function testSetPhotos()
    {
        $auction = $this->getMockBuilder(Auction::class)
            ->setMethods(['setPhotos'])
            ->getMock();

        $auction->expects($this->once())
            ->method('setPhotos')
            ->with($this->getTestPhotoArray())
            ->willReturn(null);

        $decorator = $this->getDecorator($auction);
        $decorator->setPhotos($this->getTestPhotoArray());
    }


    public function testToApiRepresentation()
    {
        $expectedResult = [
            'whatever' => 'just testing if it is forwarded correctly'
        ];

        $auction = $this->getMockBuilder(Auction::class)
            ->setMethods(['toApiRepresentation'])
            ->getMock();

        $auction->expects($this->once())
            ->method('toApiRepresentation')
            ->willReturn($expectedResult);

        $decorator = $this->getDecorator($auction);
        $result = $decorator->toApiRepresentation();
        $this->assertSame($expectedResult, $result);
    }


    public function testFromApiRepresentation()
    {
        $expectedArgument = [
            'whatever' => 'just testing if it is forwarded correctly'
        ];

        $auction = $this->getMockBuilder(Auction::class)
            ->setMethods(['fromApiRepresentation'])
            ->getMock();

        $auction->expects($this->once())
            ->method('fromApiRepresentation')
            ->with($this->equalTo($expectedArgument));


        $decorator = $this->getDecorator($auction);
        $result = $decorator->fromApiRepresentation($expectedArgument);
    }

}
