<?php

namespace Radowoj\Yaah\Decorators;

use Radowoj\Yaah\Exception;
use Radowoj\Yaah\AuctionInterface;
use Radowoj\Yaah\Constants\AuctionFids;

/**
 * Each category supported by your application should be represented by its own Decorator class,
 * as even two similar Allegro categories can have same field under different FIDs,
 * for example in Magic: The Gathering cards:
 * - category id 6089 (artifacts) has condition (new/used) under fid 26013
 * - category id 6090 (white) has condition under fid 20624
 */

abstract class AuctionArrayMapDecorator implements AuctionInterface
{

    protected $auction = null;

    /**
     * This function should return array of mappings (human readable key => Allegro WebAPI FID)
     * @return array
     */
    abstract protected function getMap();


    /**
     * This function should return id of Allegro category related to concrete decorator class
     * @return integer
     */
    abstract protected function getIdCategory();


    public function __construct(AuctionInterface $auction)
    {
        $this->auction = $auction;
    }


    /**
     * Makes sure that if a concrete class represents specific category, category id passed in fields array is correct:
     * - set it as default, if none was provided
     * - throw an exception, if some other category id was provided in $fields
     * @param  array  $fields (reference) fields array to check
     */
    protected function forceCategory(array& $fields)
    {
        //no category defined in concrete class - do nothing;
        if (!$this->getIdCategory()) {
            return;
        }

        //no category is given - default to set constant
        if (!array_key_exists(AuctionFids::FID_CATEGORY, $fields)) {
            $fields[AuctionFids::FID_CATEGORY] = $this->getIdCategory();
        }

        //wrong category is given
        if ($fields[AuctionFids::FID_CATEGORY] !== $this->getIdCategory()) {
            throw new Exception("Invalid category. {$this->getIdCategory()} expected, {$fields[AuctionFids::FID_CATEGORY]} given.");
        }

    }


    /**
     * Remap field keys using given map
     * @param  array   $array to remap
     * @param  array   $map   to remap with
     * @return array remapped array
     */
    protected function remap(array $array, array $map)
    {
        $result = [];

        foreach($array as $key => $value) {
            if (array_key_exists($key, $map)) {
                $result[$map[$key]] = $value;
            }
        }

        return $result;
    }

    /**
     * @see Radowoj\Yaah\Auction::fromArray()
     * @param  array   $humanReadableArray with human readable keys
     */
    public function fromArray(array $humanReadableArray)
    {
        $map = $this->getMap();
        $fields = $this->remap($humanReadableArray, $map);
        $this->forceCategory($fields);
        $this->auction->fromArray($fields);
    }


    /**
     * @see Radowoj\Yaah\Auction::toArray()
     * @return array with human readable keys
     */
    public function toArray()
    {
        $map = $this->getMap();
        $fields = $this->auction->toArray();
        $this->forceCategory($fields);
        $flippedMap = array_flip($map);
        return $this->remap($fields, $flippedMap);
    }


    /**
     * @see Radowoj\Yaah\Auction::toApiRepresentation()
     */
    public function toApiRepresentation()
    {
        return $this->auction->toApiRepresentation();
    }


    /**
     * @see Radowoj\Yaah\Auction::fromApiRepresentation()
     */
    public function fromApiRepresentation(array $fields)
    {
        $this->auction->fromApiRepresentation($fields);
    }


    /**
     * @see Radowoj\Yaah\Auction::setPhotos()
     */
    public function setPhotos(array $photos)
    {
        $this->auction->setPhotos($photos);
    }


}
