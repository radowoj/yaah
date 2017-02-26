<?php namespace Radowoj\Yaah;

class Field
{
    const VALUE_IMAGE = 'fvalueImage';

    protected $fid = null;

    protected $fvalueString = '';

    protected $fvalueInt = 0;

    protected $fvalueFloat = 0;

    protected $fvalueImage = 0;

    protected $fvalueDatetime = 0;

    protected $fvalueDate = '';

    protected $fvalueRangeInt = [
        'fvalueRangeIntMin' => 0,
        'fvalueRangeIntMax' => 0,
    ];

    protected $fvalueRangeFloat = [
        'fvalueRangeFloatMin' => 0,
        'fvalueRangeFloatMax' => 0,
    ];

    protected $fvalueRangeDate = [
        'fvalueRangeDateMin' => '',
        'fvalueRangeDateMax' => '',
    ];

    /**
     * @param integer $fid WebAPI fid for given field
     * @param mixed $value value for given field
     * @param string $forceValueType value type to force (i.e. fvalueImage)
     */
    public function __construct($fid, $value = null, $forceValueType = null)
    {
        $this->fid = $fid;

        if ($forceValueType) {
            if (!property_exists($this, $forceValueType)) {
                throw new Exception("Class " . get_class($this) . " does not have property: {$forceValueType}");
            }

            $this->{$forceValueType} = $value;
            return;
        }

        //if no forced value type is given, autodetect

        //@TODO - date, range autodetection

        if (is_integer($value)) {
            $this->fvalueInt = $value;
        } elseif (is_float($value)) {
            $this->fvalueFloat = $value;
        } elseif (is_string($value)) {
            $this->fvalueString = $value;
        } else {
            throw new Exception('Not supported value type: ' . gettype($value) . "; fid={$fid}");
        }
    }

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



}
