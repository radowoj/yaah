<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once 'config.php';

use Radowoj\Yaah\Auction;
use Radowoj\Yaah\Constants\AuctionTimespans;
use Radowoj\Yaah\Constants\AuctionFids;
use Radowoj\Yaah\Constants\SaleFormats;
use Radowoj\Yaah\Constants\ShippingPaidBy;
use Radowoj\Yaah\Constants\PaymentForms;
use Radowoj\Yaah\HelperFactory\Factory;

try {
    $helper = (new Factory())->create(require('config.php'));

    $localId = 1;

    $auction = new Auction([
        AuctionFids::FID_TITLE => 'Test auction',
        AuctionFids::FID_CATEGORY => 26319,
        AuctionFids::FID_TIMESPAN => AuctionTimespans::TIMESPAN_7_DAYS,
        AuctionFids::FID_QUANTITY => 1,
        AuctionFids::FID_BUY_NOW_PRICE => 10.00,
        AuctionFids::FID_COUNTRY => 1,
        AuctionFids::FID_REGION => 15,
        AuctionFids::FID_CITY => 'Poznan',
        AuctionFids::FID_SHIPPING_PAID_BY => ShippingPaidBy::SHIPPING_PAID_BY_BUYER,
        AuctionFids::FID_PAYMENT_FORMS => PaymentForms::PAYMENT_FORM_VAT_INVOICE,
        AuctionFids::FID_DESCRIPTION => 'Test auction description',
        AuctionFids::FID_POSTCODE => '12-345',
        AuctionFids::FID_SALE_FORMAT => SaleFormats::SALE_FORMAT_NON_SHOP,
        AuctionFids::FID_POST_PACKAGE_PRIORITY_PRICE => 11.00
    ]);

    $allegroItemId = $helper->newAuction($auction, $localId);

    echo "Created auction with itemId = {$allegroItemId}\n";

} catch (Exception $e) {
    echo "Exception: {$e->getMessage()}\nFile: {$e->getFile()}; Line: {$e->getLine()}\n\n";
}
