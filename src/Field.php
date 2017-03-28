<?php

namespace Radowoj\Yaah;

use InvalidArgumentException;

/**
 * Class representation of WebAPI auction field
 */
class Field
{
    const DATE_REGEX = '/^\d{2}\-\d{2}\-\d{4}$/';

    const VALUE_STRING = 'fvalueString';
    const VALUE_INT = 'fvalueInt';
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
     * array of fValues of this Field
     * @var array
     */
    protected $fValues = [];


    /**
     * @param integer $fid WebAPI fid for given field
     * @param mixed $value value for given field
     * @param string $forceValueType value type to force (i.e. fvalueImage)
     */
    public function __construct($fid = 0, $value = null, $forceValueType = '')
    {
        $this->setFid($fid);
        $this->fValues = $this->getDefaults();

        //null value should result in field with default values
        if (is_null($value)) {
            return;
        }

        //if value type was specified (useful for fvalueImage, fvalueDatetime etc.)
        if ($forceValueType) {
            $this->setValueForced($forceValueType, $value);
            return;
        }

        //if no forced value type is given, autodetect it
        $this->setValueAutodetect($value);
    }


    /**
     * Default values, "empty" WebAPI fields item
     * @return array
     */
    protected function getDefaults()
    {
        return [
            self::VALUE_STRING => self::DEFAULT_STRING,
            self::VALUE_INT => self::DEFAULT_INT,
            self::VALUE_FLOAT => self::DEFAULT_FLOAT,
            self::VALUE_IMAGE => self::DEFAULT_IMAGE,
            self::VALUE_DATETIME => self::DEFAULT_DATETIME,
            self::VALUE_DATE => self::DEFAULT_DATE,
            self::VALUE_RANGE_INT => [
                self::VALUE_RANGE_INT . 'Min' => self::DEFAULT_INT,
                self::VALUE_RANGE_INT . 'Max' => self::DEFAULT_INT,
            ],
            self::VALUE_RANGE_FLOAT => [
                self::VALUE_RANGE_FLOAT . 'Min' => self::DEFAULT_FLOAT,
                self::VALUE_RANGE_FLOAT . 'Max' => self::DEFAULT_FLOAT,
            ],
            self::VALUE_RANGE_DATE => [
                self::VALUE_RANGE_DATE . 'Min' => self::DEFAULT_DATE,
                self::VALUE_RANGE_DATE . 'Max' => self::DEFAULT_DATE,
            ],
        ];
    }


    /**
     * Set fid of this Field
     * @param integer $fid
     */
    public function setFid($fid)
    {
        if (!is_integer($fid)) {
            throw new InvalidArgumentException('fid must be an integer, ' . gettype($fid) . ' given');
        }
        $this->fid = $fid;
    }


    /**
     * Set value to fValue index of corresponding type
     * @param mixed $value
     */
    protected function setValueAutodetect($value)
    {
        if (is_integer($value)) {
            $this->fValues[self::VALUE_INT] = $value;
        } elseif (is_float($value)) {
            $this->fValues[self::VALUE_FLOAT] = $value;
        } elseif (is_string($value)) {
            $this->setValueStringAutodetect($value);
        } elseif (is_array($value)) {
            $this->setValueRangeAutodetect($value);
        } else {
            throw new InvalidArgumentException('Not supported value type: ' . gettype($value) . "; fid={$this->fid}");
        }
    }


    /**
     * Detect type of string value (date or normal string)
     * @param string $value value to detect type
     */
    protected function setValueStringAutodetect($value)
    {
        if (preg_match(self::DATE_REGEX, $value)) {
            $this->fValues[self::VALUE_DATE] = $value;
        } else {
            $this->fValues[self::VALUE_STRING] = $value;
        }
    }


    /**
     * Detect type of range passed as argument (int, float, date)
     * @param array $value value to detect type of
     */
    protected function setValueRangeAutodetect(array $range)
    {
        if (count($range) !== 2) {
            throw new InvalidArgumentException('Range array must have exactly 2 elements');
        }

        //make sure array has numeric keys
        $range = array_values($range);

        if ($this->isRangeFloat($range)) {
            $this->setRangeFloat($range);
        } elseif ($this->isRangeInt($range)) {
            $this->setRangeInt($range);
        } elseif ($this->isRangeDate($range)) {
            $this->setRangeDate($range);
        }
    }


    /**
     * Sets float range values from given array
     * @param array $range array of two float values
     */
    protected function setRangeFloat(array $range)
    {
        asort($range);
        $this->fValues[self::VALUE_RANGE_FLOAT] = array_combine(
            ['fvalueRangeFloatMin', 'fvalueRangeFloatMax'],
            $range
        );
    }


    /**
     * Sets int range values from given array
     * @param array $range array of two int values
     */
    protected function setRangeInt(array $range)
    {
        asort($range);
        $this->fValues[self::VALUE_RANGE_INT] = array_combine(
            ['fvalueRangeIntMin', 'fvalueRangeIntMax'],
            $range
        );
    }


    /**
     * Sets date range values from given array
     * @param array $range array of two date values
     */
    protected function setRangeDate(array $range)
    {
        usort($range, function($date1, $date2){
            return strtotime($date1) - strtotime($date2);
        });
        $this->fValues[self::VALUE_RANGE_DATE] = array_combine(
            ['fvalueRangeDateMin', 'fvalueRangeDateMax'],
            $range
        );
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


    /**
     * Checks if given range is date
     * @param  array   $range range to check
     * @return boolean
     */
    public function isRangeDate(array $range)
    {
        $dates = array_filter($range, function($item){
            return preg_match(self::DATE_REGEX, $item);
        });

        return (count($dates) == 2);
    }


    /**
     * Set value of arbitrary type
     * @param string $forceValueType type ('fvalueString', 'fvalueInt', ...)
     * @param mixed $value to set
     */
    protected function setValueForced($forceValueType, $value)
    {
        if (!array_key_exists($forceValueType, $this->fValues)) {
            throw new InvalidArgumentException("Class " . get_class($this) . " does not have property: {$forceValueType}");
        }

        $this->fValues[$forceValueType] = $value;
    }


    /**
     * Returns WebAPI representation of Field
     * @return array field
     */
    public function toArray()
    {
        $this->fValues['fid'] = $this->fid;
        return $this->fValues;
    }


    /**
     * Creates object from WebAPI representation of Field
     */
    public function fromArray(array $array)
    {
        //recursive object to array :)
        $array = json_decode(json_encode($array), true);

        if (!array_key_exists('fid', $array)) {
            throw new InvalidArgumentException('Fid is required');
        }

        $this->setFid($array['fid']);
        unset($array['fid']);

        foreach ($array as $key => $value) {
            if (!array_key_exists($key, $this->fValues)) {
                throw new InvalidArgumentException("Unknown Field property: {$key}");
            }

            $this->fValues[$key] = $value;
        }
    }


    /**
     * Return field fid
     * @return integer
     */
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
        $defaults = $this->getDefaults();
        foreach ($this->fValues as $key => $fValue) {
            if ($fValue !== $defaults[$key]) {
                return is_array($fValue) ? array_values($fValue) : $fValue;
            }
        }

        //if all values are at defaults, we're unable to determine
        //which one was set without additional business logic involving
        //fids - especially if the defaults come from WebAPI (fromArray())
        return null;
    }


}
