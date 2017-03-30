<?php

namespace Radowoj\Yaah\Journal;

interface DealInterface
{
    public function getEventId();

    public function getEventType();

    public function getEventTime($dateFormat = 'Y-m-d H:i:s');

    public function getId();

    public function getTransactionId();

    public function getSellerId();

    public function getItemId();

    public function getBuyerId();

    public function getQuantity();

    public function isTypeCreateDeal();

    public function isTypeCreatePostSaleForm();

    public function isTypeAbortPostSaleForm();

    public function isTypeFinishDeal();

}
