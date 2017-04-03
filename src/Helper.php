<?php

namespace Radowoj\Yaah;

use Radowoj\Yaah\Constants\SellFormOpts;
use Radowoj\Yaah\Journal\Deal;

class Helper
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
        $data = $this->client->doGetSellFormFieldsForCategory(['categoryId' => $idCategory]);

        if (!isset($data->sellFormFieldsForCategory->sellFormFieldsList->item)) {
            throw new Exception('Invalid WebAPI response: ' . print_r($data, 1));
        }

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
    public function newAuction(AuctionInterface $auction, $localId)
    {
        $auctionArray = $auction->toApiRepresentation();
        $auctionArray['localId'] = $localId;

        $this->client->doNewAuctionExt($auctionArray);
        $resultVerify = $this->client->doVerifyItem(['localId' => $localId]);

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

        return $this->client->doFinishItems(['finishItemsList' => $finishItemsList]);
    }

    /**
     * Change quantity of items available in auction
     * @param  integer $auctionId   itemId of auction to change quantity
     * @param  integer $newQuantity new quantity to set
     * @return array
     */
    public function changeQuantity($auctionId, $newQuantity)
    {
        return $this->client->doChangeQuantityItem([
            'itemId' => $auctionId,
            'newItemQuantity' => $newQuantity
        ]);
    }


    /**
     * Return site journal deals
     * @param  integer $journalStart start point (dealEventId)
     * @return array
     */
    public function getSiteJournalDeals($journalStart = 0)
    {
        $response = $this->client->doGetSiteJournalDeals(['journalStart' => $journalStart]);
        if (isset($response->siteJournalDeals->item)) {
            return array_map(function($deal){
                return new Deal($deal);
            }, $response->siteJournalDeals->item);
        }

        throw new Exception("Unable to get site journal deals: " . print_r($response, 1));
    }


    /**
     * @param  integer $itemId id of auction to get
     * @return Auction | null
     */
    public function getAuctionByItemId($itemId)
    {
        $response =  $this->client->doGetItemFields(['itemId' => $itemId]);
        if (!isset($response->itemFields->item)) {
            throw new Exception('Invalid WebAPI response: ' . print_r($response, 1));
        }

        $auction = new Auction();
        $auction->fromApiRepresentation($response->itemFields->item);
        return $auction;
    }


    /**
     * Directly call WebAPI method (prefixed by "do")
     * @param  string $name method name
     * @param  [type] $args method arguments
     * @return [type]       [description]
     */
    public function __call($name, $args)
    {
        if (strpos($name, 'do') !== 0) {
            throw new Exception("Method {$name} is not implemented in " . get_class($this));
        }

        return call_user_func_array([$this->client, $name], $args);
    }

}
