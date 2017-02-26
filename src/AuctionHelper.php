<?php namespace Radowoj\Yaah;

class AuctionHelper
{
    const SELL_FORM_OPT_REQUIRED = 1;
    const SELL_FORM_OPT_OPTIONAL = 8;

    protected $client = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getFieldsByCategory($idCategory)
    {
        $data = $this->client->getSellFormFieldsForCategory(['categoryId' => $idCategory]);
        $items = $data->sellFormFieldsForCategory->sellFormFieldsList->item;

        return array_map(function ($item) {
            return [
                'fid' => $item->sellFormId,
                'title' => $item->sellFormTitle,
                'required' => ($item->sellFormOpt == self::SELL_FORM_OPT_REQUIRED),
                'options' => $item->sellFormOptsValues,
                'optionsDesc' => $item->sellFormDesc,
            ];
        }, $items);
    }


    public function newAuction(Auction $auction)
    {
        $resultNewAuction = $this->client->newAuctionExt($auction->getApiRepresentation());
        $resultVerify = $this->client->verifyItem(['localId' => $auction->getLocalId()]);

        if (!is_object($resultVerify) || !isset($resultVerify->itemId)) {
            throw new Exception("Auction has not been created: " . print_r($resultVerify, 1));
        }

        return $resultVerify->itemId;
    }


    public function finishAuction($auctionId, $cancelAllBids = 0, $finishCancelReason = '')
    {
        return $this->finishAuctions((array)($auctionId), $cancelAllBids, $finishCancelReason);
    }


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


    public function changeQuantity($auctionId, $newQuantity)
    {
        return $this->client->changeQuantityItem([
            'itemId' => $auctionId,
            'newItemQuantity' => $newQuantity
        ]);
    }


    public function getSiteJournalDeals($journalStart)
    {
        $response = $this->client->getSiteJournalDeals(['journalStart' => $journalStart]);
        if (isset($response->siteJournalDeals->item)) {
            return $response->siteJournalDeals->item;
        }

        throw new Exception("Unable to get site journal deals: " . print_r($response, 1));
    }


    public function getAuctionByItemId($itemId)
    {
        return $this->client->getItemFields(['itemId' => $itemId]);
    }


}
