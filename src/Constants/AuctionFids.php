<?php namespace Radowoj\Yaah\Constants;

class AuctionFids
{
    const FID_TITLE = 1;
    const FID_CATEGORY = 2;
    const FID_TIMESPAN = 4;
    const FID_QUANTITY = 5;
    const FID_COUNTRY = 9;
    const FID_REGION = 10;
    const FID_CITY = 11;
    const FID_PHOTO = 16;
    const FID_DESCRIPTION = 24;
    const FID_POSTCODE = 32;
    const FID_SALE_FORMAT = 29;
    const FID_BUY_NOW_PRICE = 8;
    const FID_SHIPPING_PAID_BY = 12;
    const FID_POST_PACKAGE_PRIORITY_PRICE = 38;

    //@TODO - move category-specific fids to decorator
    const FID_CONDITION = 20626;

}
