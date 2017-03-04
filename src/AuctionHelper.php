<?php

namespace Radowoj\Yaah;

use Radowoj\Yaah\Constants\SellFormOpts;

class AuctionHelper
{
    protected $client = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get simplified fields list for given category
     * @param  integer $idCategory id of category in question
     * @return array of fields metadata
     */
    public function getFieldsByCategory($idCategory)
    {
        $data = $this->client->getSellFormFieldsForCategory(['categoryId' => $idCategory]);
        $items = $data->sellFormFieldsForCategory->sellFormFieldsList->item;

        return array_map(function ($item) {
            return [
                'fid' => $item->sellFormId,
                'title' => $item->sellFormTitle,
                'required' => ($item->sellFormOpt == SellFormOpts::SELL_FORM_OPT_REQUIRED),
                'options' => $item->sellFormOptsValues,
                'optionsDesc' => $item->sellFormDesc,
            ];
        }, $items);
    }

    /**
     * Create new auction
     *
     * @throws Exception on failure
     *
     * @param  Auction $auction Auction to create
     * @param integer $localId - local item id, required by WebAPI
     * @return integer id of created auction
     */
    public function newAuction(Auction $auction, $localId)
    {
        $auctionArray = $auction->toApiRepresentation();
        $auctionArray['localId'] = $localId;

        $resultNewAuction = $this->client->newAuctionExt($auctionArray);
        $resultVerify = $this->client->verifyItem(['localId' => $localId]);

        if (!is_object($resultVerify) || !isset($resultVerify->itemId)) {
            throw new Exception("Auction has not been created: " . print_r($resultVerify, 1));
        }

        return $resultVerify->itemId;
    }


    /**
     * Finish a single auction
     * @param  integer $auctionId itemId of auction to finish
     * @param  integer $cancelAllBids whether to cancel all bids
     * @param  string  $finishCancelReason reason
     * @return array
     */
    public function finishAuction($auctionId, $cancelAllBids = 0, $finishCancelReason = '')
    {
        return $this->finishAuctions((array)($auctionId), $cancelAllBids, $finishCancelReason);
    }


    /**
     * Finish a single auction
     * @param  array $auctionIds itemIds of auctions to finish
     * @param  integer $cancelAllBids whether to cancel all bids
     * @param  string  $finishCancelReason reason
     * @return array
     */
    public function finishAuctions(array $auctionIds, $cancelAllBids = 0, $finishCancelReason = '')
    {
        $finishItemsList = array_map(function ($auctionId) use ($cancelAllBids, $finishCancelReason) {
            return [
                'finishItemId' => $auctionId,
                'finishCancelAllBids' => $cancelAllBids,
                'finishCancelReason' => $finishCancelReason,
            ];
        }, $auctionIds);

        return $this->client->finishItems(['finishItemsList' => $finishItemsList]);
    }

    /**
     * Change quantity of items available in auction
     * @param  integer $auctionId   itemId of auction to change quantity
     * @param  integer $newQuantity new quantity to set
     * @return array
     */
    public function changeQuantity($auctionId, $newQuantity)
    {
        return $this->client->changeQuantityItem([
            'itemId' => $auctionId,
            'newItemQuantity' => $newQuantity
        ]);
    }


    /**
     * Return site journal deals
     * @param  integer $journalStart start point (dealEventId)
     * @return array
     */
    public function getSiteJournalDeals($journalStart)
    {
        $response = $this->client->getSiteJournalDeals(['journalStart' => $journalStart]);
        if (isset($response->siteJournalDeals->item)) {
            return $response->siteJournalDeals->item;
        }

        throw new Exception("Unable to get site journal deals: " . print_r($response, 1));
    }


    /**
     * @param  integer $itemId id of auction to get
     * @return Auction | null
     */
    public function getAuctionByItemId($itemId)
    {
        $response =  $this->client->getItemFields(['itemId' => $itemId]);
        if (isset($response->itemFields->item)) {
            $auction = new Auction();
            $auction->fromApiRepresentation($response->itemFields->item);
            return $auction;
        }

        return null;
    }


}
