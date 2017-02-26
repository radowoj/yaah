<?php

require_once __DIR__ . '/../vendor/autoload.php'; //Composer autoload

require_once 'config.php';

use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Auction;
use Radowoj\Yaah\Constants\AuctionTimespans;
use Radowoj\Yaah\Constants\AuctionFids;
use Radowoj\Yaah\AuctionHelper;


function loadConfig()
{
    return include('config.php');
}

try {

    $config = new Config(
        loadConfig()
    );

    $client = new Client(
        $config
    );

    $auctionHelper = new AuctionHelper($client);

    $localId = 1;

    $auction = new Auction($localId, [
        AuctionFids::FID_TITLE => 'Allegro test auction',
        AuctionFids::FID_DESCRIPTION => 'Test auction description',
        AuctionFids::FID_CATEGORY => 6092,
        AuctionFids::FID_TIMESPAN => AuctionTimespans::TIMESPAN_3_DAYS,
        AuctionFids::FID_QUANTITY => 100,
        AuctionFids::FID_COUNTRY => 1,
        AuctionFids::FID_REGION => 15,
        AuctionFids::FID_CITY => 'SomeCity',
        AuctionFids::FID_POSTCODE => '12-345',
        AuctionFids::FID_CONDITION => Auction::CONDITION_NEW,
        AuctionFids::FID_SALE_FORMAT => Auction::SALE_FORMAT_SHOP,
        AuctionFids::FID_BUY_NOW_PRICE  => 43.21,
        AuctionFids::FID_SHIPPING_PAID_BY => Auction::SHIPPING_PAID_BY_BUYER,
        AuctionFids::FID_POST_PACKAGE_PRIORITY_PRICE => 12.34,
    ]);

    $auction->setPhotos([
        //array of paths to photo files
    ]);

    $allegroItemId = $auctionHelper->newAuction($auction);

    var_dump($allegroItemId);

} catch (Exception $e) {
    echo "Exception: {$e->getMessage()}\nFile: {$e->getFile()}; Line: {$e->getLine()}\n\n";
}
