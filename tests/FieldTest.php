<?php

namespace Radowoj\Yaah;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Field;

class FieldTest extends TestCase
{
    protected $defaultValues = [
        'fid' => 1,
        'fvalueString' => '',
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
    ];


    /**
     * @expectedException InvalidArgumentException
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
            'string' => [
                'some string',
                'fvalueString'
            ],
            'integer' => [
                42,
                'fvalueInt'
            ],
            'float' => [
                13.5,
                'fvalueFloat'
            ],
            'date' => [
                '01-03-2017',
                'fvalueDate'
            ],

        ];
    }


    /**
     * Test if various value types are properly handled
     * @dataProvider valueTypesProvider
     */
    public function testValueTypes($testValue, $arrayKey)
    {
        $field = new Field(1, $testValue);
        $this->assertArrayHasKey($arrayKey, $field->toArray(), 'Key not present in result array');
        $this->assertSame($field->toArray()[$arrayKey], $testValue, 'Different value in result array');
        $this->assertSame($field->getValue(), $testValue, 'Different getValue() return value');
    }



    public function rangeValueTypesProvider()
    {
        return [
            'float range' => [
                [10.5, 13.5],
                'fvalueRangeFloat'
            ],
            'int range' => [
                [10, 11],
                'fvalueRangeInt'
            ],
            'date range' => [
                ['03-03-2016', '01-03-2017'],
                'fvalueRangeDate'
            ],
        ];
    }


    /**
     * Test if various range types are properly handled
     * @dataProvider rangeValueTypesProvider
     */
    public function testRangeValues($testRange, $rangeKey)
    {
        $expectedArrayKeys = [
            "{$rangeKey}Min",
            "{$rangeKey}Max"
        ];

        $field = new Field(2, $testRange);
        $this->assertArrayHasKey(
            $rangeKey,
            $field->toArray(),
            'key doesn\'t exist in array representation'
        );

        $this->assertSame(
            $field->toArray()[$rangeKey],
            array_combine($expectedArrayKeys, $testRange),
            'array representation contains wrong value'
        );

        $this->assertSame(
            $field->getValue(),
            $testRange,
            'getValue() returns wrong value'
        );
    }


    /**
     * Test if range types are properly handled when reversed
     * @dataProvider rangeValueTypesProvider
     */
    public function testRangeValuesReversed($testRange, $rangeKey)
    {
        $expectedArrayKeys = [
            "{$rangeKey}Min",
            "{$rangeKey}Max"
        ];

        $reverseRange = array_reverse($testRange);

        $field = new Field(2, $reverseRange);
        $this->assertArrayHasKey(
            $rangeKey,
            $field->toArray(),
            'key doesn\'t exist in array representation'
        );

        $this->assertSame(
            $field->toArray()[$rangeKey],
            array_combine($expectedArrayKeys, $testRange),
            'array representation contains wrong value'
        );

        $this->assertSame(
            $field->getValue(),
            $testRange,
            'getValue() returns wrong value'
        );
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Not supported value type: object; fid=1
     */
    public function testExceptionOnInvalidValue()
    {
        new Field(1, (object)['foo' => 'bar']);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class Radowoj\Yaah\Field does not have property: fvalueUnicorn
     */
    public function testExceptionOnInvalidForcedValue()
    {
        new Field(1, 'something', 'fvalueUnicorn');
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


    /**
     * @dataProvider valueTypesProvider
     */
    public function testCreatingFromArray($value, $key)
    {
        $array = $this->defaultValues;
        $array[$key] = $value;
        $field = new Field(42);
        $field->fromArray($array);

        $this->assertSame($field->getValue(), $value);
        $this->assertSame($field->getFid(), $this->defaultValues['fid']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Range array must have exactly 2 elements
     */
    public function testExceptionOnInvalidRangeArrayItemCount()
    {
        $field = new Field(12, [1, 2, 3]);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Fid is required
     */
    public function testExceptionOnFromArrayMissingFid()
    {
        $fieldArray = $this->defaultValues;
        unset($fieldArray['fid']);
        $field = new Field();
        $field->fromArray($fieldArray);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown Field property: thisKeyIsInvalid
     */
    public function testExceptionOnFromArrayInvalidArrayKey()
    {
        $fieldArray = $this->defaultValues;
        $fieldArray['thisKeyIsInvalid'] = 'whatever';
        $field = new Field();
        $field->fromArray($fieldArray);
    }


    public function testNullReturnValueWhenAllFieldsAreDefault()
    {
        $field = new Field();
        $field->fromArray($this->defaultValues);
        $this->assertNull($field->getValue());
    }


}
