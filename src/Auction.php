<?php

namespace Radowoj\Yaah;

use Radowoj\Yaah\Constants\AuctionFids;
use InvalidArgumentException;

class Auction implements AuctionInterface
{
    const MAX_PHOTOS = 8;

    protected $fields = [];

    protected $photos = [];

    public function __construct(array $fields = [])
    {
        $this->setFields($fields);
    }


    /**
     * Sets photos for auction
     * @param array $photos array of photo file paths
     */
    public function setPhotos(array $photos)
    {
        $photosCount = count($photos);

        if ($photosCount > self::MAX_PHOTOS) {
            throw new InvalidArgumentException("Photo files limit exceeded, " . self::MAX_PHOTOS . " allowed, " . $photosCount . " given");
        }

        $this->photos = $photos;
    }


    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }


    /**
     * Returns WebAPI's representation of an auction (array of fields)
     * @return array
     */
    public function toApiRepresentation()
    {
        $fields = [];

        foreach ($this->fields as $fid => $value) {
            $fields[] = (new Field($fid, $value))->toArray();
        }

        $this->addPhotoFields($fields);

        return [
            'fields' => $fields,
        ];
    }


    /**
     * Creates an auction from WebAPI's representation (array of fields)
     * @param  array  $fields
     */
    public function fromApiRepresentation(array $fields)
    {
        $this->fields = [];
        $this->photos = [];

        foreach ($fields as $apiField) {
            $field = new Field();
            $field->fromArray((array)$apiField);
            $this->fields[$field->getFid()] = $field->getValue();
        }
    }


    /**
     * Add photos to given array of Fields
     * @param array& $fields array of Fields to extend with photos
     */
    protected function addPhotoFields(array& $fields)
    {
        $count = count($this->photos);
        if (!$count) {
            return;
        }

        $index = 0;
        foreach ($this->photos as $photo) {
            if (!is_readable($photo)) {
                throw new Exception("Photo file {$photo} is not readable");
            }

            $fields[] = (new Field(AuctionFids::FID_PHOTO + $index, file_get_contents($photo), Field::VALUE_IMAGE))->toArray();
            $index++;
        }
    }


    /**
     * Simplified array representation (similar to constructor params)
     * @return array
     */
    public function toArray()
    {
        $fields = $this->fields;
        $this->addPhotoFields($fields);
        return $this->fields;
    }

}
