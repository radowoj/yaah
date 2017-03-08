<?php

namespace Radowoj\Yaah;

interface AuctionInterface
{
    public function toApiRepresentation();

    public function fromApiRepresentation(array $fields);

    public function setPhotos(array $photos);

    public function setFields(array $fields);

    public function toArray();

}
