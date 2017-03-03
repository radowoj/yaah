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
        new Field('some string');
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
            'fvalueImage' => '',
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


    public function valueTypesProvider()
    {
        return [
            'string' => ['some string', 'fvalueString'],
            'integer' => [42, 'fvalueInt'],
            'float' => [13.5, 'fvalueFloat'],
            'date' => ['01-03-2017', 'fvalueDate'],
        ];
    }


    /**
     * Test if various value types are properly handled
     * @dataProvider valueTypesProvider
     */
    public function testValueTypes($testValue, $arrayKey)
    {
        $field = new Field(1, $testValue);
        $this->assertArrayHasKey($arrayKey, $field->toArray());
        $this->assertSame($field->toArray()[$arrayKey], $testValue);
        $this->assertSame($field->getValue(), $testValue);
    }


    /**
     * Test forced value type - datetime
     * @return void
     */
    public function testDatetimeValue()
    {
        $datetime = time();
        $field = new Field(1, $datetime, 'fvalueDatetime');
        $this->assertArrayHasKey('fvalueDatetime', $field->toArray());
        $this->assertSame($field->toArray()['fvalueDatetime'], $datetime);
        $this->assertSame($field->getValue(), $datetime);
    }

    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Not supported value type: object; fid=1
     */
    public function testExceptionOnInvalidValue()
    {
        $field = new Field(1, (object)['foo' => 'bar']);
    }


    /**
     * @expectedException Radowoj\Yaah\Exception
     * @expectedExceptionMessage Class Radowoj\Yaah\Field does not have property: fvalueUnicorn
     */
    public function testExceptionOnInvalidForcedValue()
    {
        $field = new Field(1, 'something', 'fvalueUnicorn');
    }


    /**
     * Test fid value
     * @return void
     */
    public function testFid()
    {
        $field = new Field(42, 'don\'t panic!');
        $this->assertSame($field->getFid(), 42);
    }

}
