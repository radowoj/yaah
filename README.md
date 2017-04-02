# yaah - Yet Another Allegro Helper :)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/radowoj/yaah/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/radowoj/yaah/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/radowoj/yaah/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/radowoj/yaah/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/radowoj/yaah/badges/build.png?b=master)](https://scrutinizer-ci.com/g/radowoj/yaah/build-status/master)

Simple client library for Allegro WebAPI.

## Goals

* Simplify basic auction operations (creating new auction, finishing, changing item quantity, retrieving log etc) - Helper class.
* Avoid the necessity of sending defaults for every value type of [every required field](http://allegro.pl/webapi/documentation.php/show/id,113#method-input) (fvalueString, fvalueInt, fvalueDate, fvalueAndSoOn...) - Auction class and [its decorators](https://github.com/radowoj/yaah-mtg).

## Installation

Via composer

```bash
$ composer require radowoj/yaah
```

## Example 1 - new auction

For an example of a more programmer-friendly Auction interface, see [yaah-mtg](https://github.com/radowoj/yaah-mtg)

```php
use Radowoj\Yaah\Auction;
use Radowoj\Yaah\Constants\AuctionTimespans;
use Radowoj\Yaah\Constants\AuctionFids;
use Radowoj\Yaah\Constants\SaleFormats;
use Radowoj\Yaah\Constants\ShippingPaidBy;
use Radowoj\Yaah\Constants\PaymentForms;
use Radowoj\Yaah\HelperFactory\Factory;

$helper = (new Factory())->create([
    'apiKey'        => 'your-allegro-api-key',
    'login'         => 'your-allegro-login',
    'passwordHash'  => 'your-sha-256-hashed-and-then-base64-encoded-allegro-password',
    'countryCode'   => 'your-country-code',
    'isSandbox'     => 'true-if-you-intend-to-use-sandbox'
]);

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

$auction->setPhotos([
    //array of (no more than 8) paths to photo files
]);

$allegroItemId = $helper->newAuction($auction, $localId);

echo "Created auction with itemId = {$allegroItemId}\n";
```

## Example 2 - retrieving journal

```php
require_once __DIR__ . '/../vendor/autoload.php';

require_once 'config.php';

use Radowoj\Yaah\HelperFactory\Factory;

try {
    $helper = (new Factory())->create(require('config.php'));

    $deals = $helper->getSiteJournalDeals();

    /**
     * Process journal entries - in this case echo details of every journal
     * item that creates a deal
     */
    array_map(function($deal){
        if ($deal->isTypeCreateDeal()) {
            echo "Deal {$deal->getId()} (itemId {$deal->getItemId()} - quantity {$deal->getQuantity()}) created at {$deal->getEventTime()}\n";
        }
    }, $deals);

} catch (Exception $e) {
    echo "Exception: {$e->getMessage()}\nFile: {$e->getFile()}; Line: {$e->getLine()}\n\n";
}
```
