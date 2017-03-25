<?php

namespace Radowoj\Yaah\Decorators;

use Radowoj\Yaah\AuctionInterface;

abstract class AuctionArrayMapDecorator implements AuctionInterface
{
    /**
     * One decorator should represent one category, as even two Allegro categories can
     * have same field under different FIDs, for example in Magic: The Gathering cards:
     * - category id 6089 (artifacts) has condition (new/used) under fid 26013
     * - category id 6090 (white) has condition under fid 20624
     */

    protected $auction = null;

    /**
     * This function should return array of mappings (human readable key => Allegro WebAPI FID)
     * @return array
     */
    abstract protected function getMap();

    public function __construct(AuctionInterface $auction)
    {
        $this->auction = $auction;
    }


    public function fromArray(array $humanReadableArray)
    {
        $map = $this->getMap();
        $fields = [];
        foreach($humanReadableArray as $key => $value) {
            if (array_key_exists($key, $map)) {
                $fields[$map[$key]] = $value;
            }
        }
        $this->auction->fromArray($fields);
    }


    public function toArray()
    {
        $map = $this->getMap();
        $fields = $this->auction->toArray();
        $flippedMap = array_flip($map);
        $humanReadableArray = [];

        foreach($fields as $key => $value) {
            if (array_key_exists($key, $flippedMap)) {
                $humanReadableArray[$flippedMap[$key]] = $value;
            }
        }

        return $humanReadableArray;
    }


    public function toApiRepresentation()
    {
        return $this->auction->toApiRepresentation();
    }

    public function fromApiRepresentation(array $fields)
    {
        $this->auction->fromApiRepresentation($fields);
    }

    public function setPhotos(array $photos)
    {
        $this->auction->setPhotos($photos);
    }



}
