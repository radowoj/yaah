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
}
