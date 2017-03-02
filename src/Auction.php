<?php

namespace Radowoj\Yaah;

use Radowoj\Yaah\Constants\AuctionFids;

class Auction
{
    const MAX_PHOTOS = 8;

    protected $fields = [];

    protected $photos = [];

    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }


    public function setPhotos(array $photos)
    {
        $photosCount = count($photos);

        if ($photosCount > self::MAX_PHOTOS) {
            throw new Exception("Photo files limit exceeded, " . self::MAX_PHOTOS . " allowed, " . $photosCount . " given");
        }

        $this->photos = $photos;
    }


    public function toApiRepresentation()
    {
        $fields = [];

        foreach($this->fields as $fid => $value) {
            $fields[] = (new Field($fid, $value))->toArray();
        }

        $this->addPhotoFields($fields);

        return [
            'fields' => $fields,
        ];
    }


    public function fromApiRepresentation(array $fields)
    {
        $this->fields = [];
        $this->photos = [];

        foreach($fields as $apiField) {
            $field = new Field(0, '');
            $field->fromArray((array)$apiField);
            $this->fields[$field->getFid()] = $field->getValue();
        }
    }


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

}
