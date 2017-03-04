<?php

namespace Radowoj\Yaah;

/**
 * Class representation of WebAPI auction field
 */
class Field
{
    const VALUE_STRING = 'fvalueString';
    const VALUE_INTEGER = 'fvalueInt';
    const VALUE_FLOAT = 'fvalueFloat';
    const VALUE_IMAGE = 'fvalueImage';
    const VALUE_DATETIME = 'fvalueDatetime';
    const VALUE_DATE = 'fvalueDate';
    const VALUE_RANGE_INT = 'fvalueRangeInt';
    const VALUE_RANGE_FLOAT = 'fvalueRangeFloat';
    const VALUE_RANGE_DATE = 'fvalueRangeDate';


    const DEFAULT_STRING = '';
    const DEFAULT_INT = 0;
    const DEFAULT_FLOAT = 0;
    const DEFAULT_IMAGE = '';
    const DEFAULT_DATETIME = 0;
    const DEFAULT_DATE = '';

    /**
     * Allegro WebAPI fid
     * @var integer
     */
    protected $fid = null;

    /**
     * String value of given field
     * @var string
     */
    protected $fvalueString = self::DEFAULT_STRING;

    /**
     * Integer value of given field
     * @var integer
     */
    protected $fvalueInt = self::DEFAULT_INT;


    /**
     * Float value of given field
     * @var float
     */
    protected $fvalueFloat = self::DEFAULT_FLOAT;

    /**
     * Image (image file content)
     * @var mixed
     */
    protected $fvalueImage = self::DEFAULT_IMAGE;

    /**
     * Unix time
     * @var float
     */
    protected $fvalueDatetime = self::DEFAULT_DATETIME;

    /**
     * Date (dd-mm-yyyy)
     * @var string
     */
    protected $fvalueDate = self::DEFAULT_DATE;

    /**
     * Integer range
     * @var array
     */
    protected $fvalueRangeInt = [
        'fvalueRangeIntMin' => self::DEFAULT_INT,
        'fvalueRangeIntMax' => self::DEFAULT_INT,
    ];

    /**
     * Float range
     * @var array
     */
    protected $fvalueRangeFloat = [
        'fvalueRangeFloatMin' => self::DEFAULT_FLOAT,
        'fvalueRangeFloatMax' => self::DEFAULT_FLOAT,
    ];

    /**
     * Date range
     * @var array
     */
    protected $fvalueRangeDate = [
        'fvalueRangeDateMin' => self::DEFAULT_DATE,
        'fvalueRangeDateMax' => self::DEFAULT_DATE,
    ];

    /**
     * @param integer $fid WebAPI fid for given field
     * @param mixed $value value for given field
     * @param string $forceValueType value type to force (i.e. fvalueImage)
     */
    public function __construct($fid, $value = null, $forceValueType = '')
    {
        if (!is_integer($fid)) {
            throw new Exception('fid must be an integer, ' . gettype($fid) . ' given');
        }
        $this->fid = $fid;

        //if value type was specified (useful for fvalueImage, fvalueDatetime etc.)
        if ($forceValueType) {
            $this->setValueForced($forceValueType, $value);
            return;
        }

        //if no forced value type is given, autodetect it
        $this->setValueAutodetect($value);
    }


    protected function setValueAutodetect($value)
    {
        if (is_integer($value)) {
            $this->fvalueInt = $value;
        } elseif (is_float($value)) {
            $this->fvalueFloat = $value;
        } elseif (is_string($value)) {
            $this->setValueStringAutodetect($value);
        } elseif (is_array($value)) {
            $this->setValueRangeAutodetect($value);
        } else {
            throw new Exception('Not supported value type: ' . gettype($value) . "; fid={$this->fid}");
        }
    }


    /**
     * Detect type of string value (date or normal string)
     * @param string $value value to detect type
     */
    protected function setValueStringAutodetect($value)
    {
        if (preg_match('/^\d{2}\-\d{2}\-\d{4}$/', $value)) {
            $this->fvalueDate = $value;
        } else {
            $this->fvalueString = $value;
        }
    }

    /**
     * Detect type of range passed as argument (int, float, date)
     * @param array $value value to detect type
     */
    protected function setValueRangeAutodetect(array $range)
    {
        if (count($range) !== 2) {
            throw new Exception('Range array must have exactly 2 elements');
        }

        //make sure array has numeric keys
        $range = array_values($range);

        asort($range);

        if ($this->isRangeFloat($range)) {
            $this->fvalueRangeFloat = array_combine(
                ['fvalueRangeFloatMin', 'fvalueRangeFloatMax'],
                $range
            );
        } elseif($this->isRangeInt($range)) {
            $this->fvalueRangeInt = array_combine(
                ['fvalueRangeIntMin', 'fvalueRangeIntMax'],
                $range
            );
        }
    }


    /**
     * Checks if given range is float
     * @param  array   $range range to check
     * @return boolean
     */
    protected function isRangeFloat(array $range)
    {
        $floats = array_filter($range, 'is_float');
        return (count($floats) == 2);
    }


    /**
     * Checks if given range is int
     * @param  array   $range range to check
     * @return boolean
     */
    protected function isRangeInt(array $range)
    {
        $ints = array_filter($range, 'is_int');
        return (count($ints) == 2);
    }



    protected function setValueForced($forceValueType, $value)
    {
        if (!property_exists($this, $forceValueType)) {
            throw new Exception("Class " . get_class($this) . " does not have property: {$forceValueType}");
        }

        $this->{$forceValueType} = $value;
    }


    /**
     * Returns WebAPI representation of Field
     * @return array field
     */
    public function toArray()
    {
        return [
            'fid' => $this->fid,
            'fvalueString' => $this->fvalueString,
            'fvalueInt' => $this->fvalueInt,
            'fvalueFloat' => $this->fvalueFloat,
            'fvalueImage' => $this->fvalueImage,
            'fvalueDatetime' => $this->fvalueDatetime,
            'fvalueDate' => $this->fvalueDate,
            'fvalueRangeInt' => $this->fvalueRangeInt,
            'fvalueRangeFloat' => $this->fvalueRangeFloat,
            'fvalueRangeDate' => $this->fvalueRangeDate,
        ];
    }


    /**
     * Creates object from WebAPI representation of Field
     */
    public function fromArray(array $array)
    {
        //recursive object to array :)
        $array = json_decode(json_encode($array), true);

        foreach($array as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new Exception("Unknown Field property: {$key}");
            }

            $this->{$key} = $value;
        }
    }


    public function getFid()
    {
        return $this->fid;
    }


    /**
     * Return first property that is different from its default value
     * @return mixed | null
     */
    public function getValue()
    {
        if ($this->fvalueString !== self::DEFAULT_STRING) {
            return $this->fvalueString;
        }

        if ($this->fvalueInt !== self::DEFAULT_INT) {
            return $this->fvalueInt;
        }

        if ($this->fvalueFloat !== self::DEFAULT_FLOAT) {
            return $this->fvalueFloat;
        }

        if ($this->fvalueImage !== self::DEFAULT_IMAGE) {
            return base64_decode($this->fvalueImage);
        }

        if ($this->fvalueDatetime !== self::DEFAULT_DATETIME) {
            return $this->fvalueDatetime;
        }

        if ($this->fvalueDate !== self::DEFAULT_DATE) {
            return $this->fvalueDate;
        }

        $rangeValue = $this->getRangeValue();
        if (!is_null($rangeValue)) {
            return $rangeValue;
        }

        //if all values are at defaults, we're unable to determine
        //which one was set without additional business logic involving
        //fids - especially if the defaults come from WebAPI (fromArray())
        return null;
    }


    protected function getRangeValue()
    {
        $rangeFloat = array_values($this->fvalueRangeFloat);
        if ($rangeFloat !== array_fill(0, 2, self::DEFAULT_FLOAT)) {
            return $rangeFloat;
        }

        $rangeInt = array_values($this->fvalueRangeInt);
        if ($rangeInt !== array_fill(0, 2, self::DEFAULT_INT)) {
            return $rangeInt;
        }

        //@TODO date ranges
        return null;
    }


}
