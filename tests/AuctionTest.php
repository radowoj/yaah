<?php

namespace Radowoj\Yaah;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Auction;

class AuctionTest extends TestCase
{

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Photo files limit exceeded, 8 allowed, 9 given
     */
    public function testExceptionOnTooManyPhotos()
    {
        $auction = new Auction();
        $auction->setPhotos([
            'maximum', 'number', 'of', 'photos', 'is', 'eight', 'so', 'test', 'nine'
        ]);
    }


    protected function prepareTestAuction()
    {
        return new Auction([
            1 => 'test string',
            2 => 42,
            3 => '05-03-2017',
            4 => 12.34,
        ]);
    }


    protected function checkTestAuctionFields(array $fields)
    {
        $testString = array_shift($fields);
        $this->assertArrayHasKey('fvalueString', $testString);
        $this->assertSame('test string', $testString['fvalueString']);

        $testInt = array_shift($fields);
        $this->assertArrayHasKey('fvalueInt', $testInt);
        $this->assertSame(42, $testInt['fvalueInt']);

        $testDate = array_shift($fields);
        $this->assertArrayHasKey('fvalueDate', $testDate);
        $this->assertSame('05-03-2017', $testDate['fvalueDate']);

        $testFloat = array_shift($fields);
        $this->assertArrayHasKey('fvalueFloat', $testFloat);
        $this->assertSame(12.34, $testFloat['fvalueFloat']);
    }


    public function testApiRepresentationWithoutPhotos()
    {
        $auction = $this->prepareTestAuction();

        $apiRepresentation = $auction->toApiRepresentation();

        $this->assertArrayHasKey('fields', $apiRepresentation);
        $this->assertEquals(4, count($apiRepresentation['fields']));

        $this->checkTestAuctionFields($apiRepresentation['fields']);
    }


    public function testApiRepresentationWithPhotos()
    {
        $auction = $this->prepareTestAuction();

        $auction->setPhotos([
            __DIR__ . '/i.am.a.photo.file.txt'
        ]);

        $apiRepresentation = $auction->toApiRepresentation();

        $this->assertArrayHasKey('fields', $apiRepresentation);
        $this->assertEquals(5, count($apiRepresentation['fields']));

        $this->checkTestAuctionFields($apiRepresentation['fields']);
    }


    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Photo file is not readable
     */
    public function testApiRepresentationWithInvalidPhotos()
    {
        $auction = $this->prepareTestAuction();

        $auction->setPhotos([
            __DIR__ . '/i.am.not.a.photo.file.txt'
        ]);

        $apiRepresentation = $auction->toApiRepresentation();

        $this->assertArrayHasKey('fields', $apiRepresentation);
        $this->assertEquals(5, count($apiRepresentation['fields']));

        $fields = $apiRepresentation['fields'];

        $this->checkTestAuctionFields($apiRepresentation['fields']);
    }

}
