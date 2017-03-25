<?php

namespace Radowoj\Yaah\Decorators;

use Radowoj\Yaah\Constants\AuctionFids;


abstract class MTGAuctionDecorator extends AuctionArrayMapDecorator
{
    protected function getMap()
    {
        return [
            'title' => AuctionFids::FID_TITLE,
            'timespan' => AuctionFids::FID_TIMESPAN,
            'description' => AuctionFids::FID_DESCRIPTION,
            'category' => AuctionFids::FID_CATEGORY,
            'quantity' => AuctionFids::FID_QUANTITY,
            'country' => AuctionFids::FID_COUNTRY,
            'region' => AuctionFids::FID_REGION,
            'city' => AuctionFids::FID_CITY,
            'postcode' => AuctionFids::FID_POSTCODE,
            'condition' => static::FID_CONDITION,
            'sale_format' => AuctionFids::FID_SALE_FORMAT,
            'buy_now_price' => AuctionFids::FID_BUY_NOW_PRICE,
            'shipping_paid_by' => AuctionFids::FID_SHIPPING_PAID_BY,
            'post_package_priority_price' => AuctionFids::FID_POST_PACKAGE_PRIORITY_PRICE,
        ];

    }

}
