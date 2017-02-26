<?php

require_once __DIR__ . '/../vendor/autoload.php'; //Composer autoload

require_once 'config.php';

use Radowoj\Yaah\Config;
use Radowoj\Yaah\Client;
use Radowoj\Yaah\Auction;
use Radowoj\Yaah\Constants\AuctionTimespans;
use Radowoj\Yaah\AuctionHelper;


function loadConfig()
{
    return include('config.php');
}


$config = new Config(
    loadConfig()
);

$client = new Client(
    $config
);

$auctionHelper = new AuctionHelper($client);

$auction = new Auction([
    'title' => 'Allegro test auction',
    'description' => 'Test auction description',
    'category' => 6092,
    'timespan' => AuctionTimespans::TIMESPAN_3_DAYS,
    'quantity' => 100,
    'country' => 1,
    'region' => 15,
    'city' => 'SomeCity',
    'postcode' => '12-345',
    'condition' => Auction::CONDITION_NEW,
    'sale_format' => Auction::SALE_FORMAT_SHOP,
    'buy_now_price' => 43.21,
    'shipping_paid_by' => Auction::SHIPPING_PAID_BY_BUYER,
    'post_package_priority_price' => 12.34,
    'local_id' => 1,
]);

$allegroItemId = $auctionHelper->newAuction($auction);

var_dump($allegroItemId);
