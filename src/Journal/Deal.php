<?php

namespace Radowoj\Yaah\Journal;

use stdClass;
use InvalidArgumentException;

/**
 * Journal entry for a single Deal event (doGetSiteJournalDeals())
 */
class Deal implements DealInterface
{
    /**
     * @see http://allegro.pl/webapi/documentation.php/show/id,742#method-output
     */
    const EVENT_TYPE_CREATE_DEAL = 1;
    const EVENT_TYPE_CREATE_POST_SALE_FORM = 2;
    const EVENT_TYPE_ABORT_POST_SALE_FORM = 3;
    const EVENT_TYPE_FINISH_DEAL = 4;

    protected $eventId = null;

    protected $eventType = null;

    protected $eventTime = null;

    protected $id = null;

    protected $transactionId = null;

    protected $sellerId = null;

    protected $itemId = null;

    protected $buyerId = null;

    protected $quantity = null;


    public function __construct(stdClass $deal)
    {
        $this->mapFromObject($deal);
    }


    /**
     * Validate and set a Deal property based on WebAPI's returned object property
     * @param string $originalProperty original (WebAPI) property name (i.e. dealEventId)
     * @param string $value value
     */
    protected function setProperty($originalProperty, $value)
    {
        if (strpos($originalProperty, 'deal') !== 0) {
            throw new InvalidArgumentException("Original deal property name must start with \"deal\"");
        }

        $localProperty = lcfirst(
            preg_replace('/^deal/', '', $originalProperty)
        );

        if (!property_exists($this, $localProperty)) {
            throw new InvalidArgumentException("Unknown Deal property: {$localProperty}");
        }

        $this->{$localProperty} = $value;
    }


    /**
     * Set properties based on WebAPI's returned object
     * @param  stdClass $deal deal object returned by doGetSiteJournalDeals()
     */
    protected function mapFromObject(stdClass $deal)
    {
        foreach($deal as $prop => $value) {
            $this->setProperty($prop, $value);
        }
    }


    public function getEventId()
    {
        return $this->eventId;
    }


    public function getEventType()
    {
        return $this->eventType;
    }


    public function getEventTime($dateFormat = 'Y-m-d H:i:s')
    {
        return date($dateFormat, $this->eventTime);
    }


    public function getId()
    {
        return $this->id;
    }


    public function getTransactionId()
    {
        return $this->transactionId;
    }


    public function getSellerId()
    {
        return $this->sellerId;
    }


    public function getItemId()
    {
        return $this->itemId;
    }


    public function getBuyerId()
    {
        return $this->buyerId;
    }


    public function getQuantity()
    {
        return $this->quantity;
    }


    public function isTypeCreateDeal()
    {
        return $this->eventType === self::EVENT_TYPE_CREATE_DEAL;
    }

    public function isTypeCreatePostSaleForm()
    {
        return $this->eventType === self::EVENT_TYPE_CREATE_POST_SALE_FORM;
    }

    public function isTypeAbortPostSaleForm()
    {
        return $this->eventType === self::EVENT_TYPE_ABORT_POST_SALE_FORM;
    }

    public function isTypeFinishDeal()
    {
        return $this->eventType === self::EVENT_TYPE_FINISH_DEAL;
    }


}
