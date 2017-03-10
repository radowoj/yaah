<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once 'config.php';

use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Auction;
use Radowoj\Yaah\Constants\AuctionTimespans;
use Radowoj\Yaah\Constants\AuctionFids;
use Radowoj\Yaah\Constants\Conditions;
use Radowoj\Yaah\Constants\SaleFormats;
use Radowoj\Yaah\Constants\ShippingPaidBy;
use Radowoj\Yaah\Helper;
use Radowoj\Yaah\Constants\Wsdl;
use Radowoj\Yaah\Decorators\MTGAuctionDecorator;

try {

    $config = new Config(
        include('config.php')
    );

    $soapClient = new SoapClient(
        $config->getIsSandbox()
            ? Wsdl::WSDL_SANDBOX
            : Wsdl::WSDL_PRODUCTION
    );

    $client = new Client(
        $config,
        $soapClient
    );

    $helper = new Helper($client);

    $mtgAuction = new MTGAuctionDecorator(new Auction());
    $mtgAuction->fromArray([
        'title' => 'Allegro test auction',
        'description' => 'Test auction description',
        'category' => 6092,
        'timespan' => AuctionTimespans::TIMESPAN_3_DAYS,
        'quantity' => 100,
        'country' => 1,
        'region' => 15,
        'city' => 'SomeCity',
        'postcode' => '12-345',
        'condition' => Conditions::CONDITION_NEW,
        'sale_format' => SaleFormats::SALE_FORMAT_SHOP,
        'buy_now_price' => 43.21,
        'shipping_paid_by' => ShippingPaidBy::SHIPPING_PAID_BY_BUYER,
        'post_package_priority_price' => 12.34,
    ]);

    $mtgAuction->setPhotos([
        //array of (no more than 8) paths to photo files
    ]);

    $localId = 1;

    $allegroItemId = $helper->newAuction($mtgAuction, $localId);

    echo "Created auction with itemId = {$allegroItemId}\n";

} catch (Exception $e) {
    echo "Exception: {$e->getMessage()}\nFile: {$e->getFile()}; Line: {$e->getLine()}\n\n";
}
