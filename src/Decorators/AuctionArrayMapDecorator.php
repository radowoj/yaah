<?php

namespace Radowoj\Yaah\Decorators;

use Radowoj\Yaah\AuctionInterface;

abstract class AuctionArrayMapDecorator implements AuctionInterface
{
    protected $auction = null;

    protected $map = [
        //human readable key => Allegro WebAPI FID
    ];


    public function __construct(AuctionInterface $auction)
    {
        $this->auction = $auction;
    }


    public function fromArray(array $humanReadableArray)
    {
        $fields = [];
        foreach($humanReadableArray as $key => $value) {
            if (array_key_exists($key, $this->map)) {
                $fields[$this->map[$key]] = $value;
            }
        }
        $this->auction->fromArray($fields);
    }


    public function toArray()
    {
        $fields = $this->auction->toArray();
        $flippedMap = array_flip($this->map);
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
