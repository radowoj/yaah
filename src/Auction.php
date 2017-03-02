<?php

namespace Radowoj\Yaah;

use Radowoj\Yaah\Constants\AuctionFids;

class Auction
{
    const MAX_PHOTOS = 8;

    protected $localId = null;

    protected $fields = [];

    protected $photos = [];

    public function __construct($localId, array $fields)
    {
        $this->localId = $localId;
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

    public function getApiRepresentation()
    {
        $fields = [];

        foreach($this->fields as $fid => $value) {
            $fields[] = (new Field($fid, $value))->toArray();
        }

        $this->addPhotoFields($fields);

        return [
            'fields' => $fields,
            'localId' => $this->getLocalId(),
        ];
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


    public function getLocalId()
    {
        return $this->localId;
    }


}
