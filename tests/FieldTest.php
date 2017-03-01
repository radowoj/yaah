<?php

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Field;

class FieldTest extends TestCase
{

    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage fid must be an integer
     */
    public function testNonIntegerFid()
    {
        $field = new Field('some string');
    }


    /**
     * Tests that output of Field->toArray() has the exact keys expected by WebAPI
     * @return void
     */
    public function testReturnedArray()
    {
        $field = new Field(1, 'some string');

        $this->assertEquals([
            'fid' => 1,
            'fvalueString' => 'some string',
            'fvalueInt' => 0,
            'fvalueFloat' => 0,
            'fvalueImage' => 0,
            'fvalueDatetime' => 0,
            'fvalueDate' => '',
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
            ],

        ], $field->toArray());
    }

    /**
     * Tests integer value representation
     * @return void
     */
    public function testIntegerValue()
    {
        $int = 10;
        $array = (new Field(1, $int))->toArray();
        $this->assertArrayHasKey('fvalueInt', $array);
        $this->assertSame($array['fvalueInt'], $int);
    }


    /**
     * Tests float value representation
     * @return void
     */
    public function testFloatValue()
    {
        $float = 13.5;
        $array = (new Field(1, $float))->toArray();
        $this->assertArrayHasKey('fvalueFloat', $array);
        $this->assertSame($array['fvalueFloat'], $float);
    }


    public function testDateValue()
    {
        $date = '01-03-2017';
        $array = (new Field(1, $date))->toArray();
        $this->assertArrayHasKey('fvalueDate', $array);
        $this->assertSame($array['fvalueDate'], $date);

    }
}
