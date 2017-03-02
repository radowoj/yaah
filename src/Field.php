<?php

namespace Radowoj\Yaah;

/**
 * Class representation of WebAPI auction field
 */
class Field
{
    const VALUE_IMAGE = 'fvalueImage';

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
    protected function setValueRangeAutodetect(array $value)
    {
        if (count($value) !== 2) {
            throw new Exception('Range array must have exactly 2 elements');
        }
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

        //@TODO ranges

        //no clue what value type it was, all are defaults, so let's return null
        return null;
    }

}
