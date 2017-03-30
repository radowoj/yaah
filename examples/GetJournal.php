<?php

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
