<?php

namespace Radowoj\Yaah\Decorators;

interface AuctionDecoratorInterface
{
    public function toArray();

    public function fromArray(array $assoc);

}
