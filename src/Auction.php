<?php namespace Radowoj\Yaah;

use Radowoj\Yaah\Constants\AuctionFids;

class Auction
{
    const CONDITION_NEW = 1;
    const CONDITION_USED = 2;

    const SHIPPING_PAID_BY_SELLER = 0;
    const SHIPPING_PAID_BY_BUYER = 1;

    const SALE_FORMAT_NON_SHOP = 0;
    const SALE_FORMAT_SHOP = 1;

    const MAX_PHOTOS = 8;

    protected $title = null;

    protected $description = null;

    protected $category = null;

    protected $timespan = null;

    protected $quantity = null;

    protected $country = null;

    protected $postcode = null;

    protected $region = null;

    protected $city = null;

    protected $condition = null;

    protected $sale_format = null;

    protected $buy_now_price = null;

    protected $shipping_paid_by = null;

    protected $post_package_priority_price = null;

    protected $local_id = null;

    protected $photos = [];

    public function __construct(array $params)
    {
        $this->checkPhotosCount($params);

        foreach ($params as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
    }

    protected function checkPhotosCount(array $params)
    {
        if (!array_key_exists('photos', $params)) {
            return;
        }

        $photosCount = count($params['photos']);

        if ($photosCount > self::MAX_PHOTOS) {
            throw new Exception("Photo files limit exceeded, " . self::MAX_PHOTOS . " allowed, " . $photosCount . " given");
        }
    }


    public function getSaleFormat()
    {
        return $this->sale_format;
    }


    public function getBuyNowPrice()
    {
        return $this->buy_now_price;
    }


    public function getShippingPaidBy()
    {
        return $this->shipping_paid_by;
    }


    public function getPostPackagePriorityPrice()
    {
        return $this->post_package_priority_price;
    }

    public function getLocalId()
    {
        return $this->local_id;
    }


    public function getApiRepresentation()
    {
        $fields = [
            (new Field(AuctionFids::FID_TITLE, $this->getTitle()))->toArray(),
            (new Field(AuctionFids::FID_CATEGORY, $this->getCategory()))->toArray(),
            (new Field(AuctionFids::FID_TIMESPAN, $this->getTimespan()))->toArray(),
            (new Field(AuctionFids::FID_QUANTITY, $this->getQuantity()))->toArray(),
            (new Field(AuctionFids::FID_COUNTRY, $this->getCountry()))->toArray(),
            (new Field(AuctionFids::FID_REGION, $this->getRegion()))->toArray(),
            (new Field(AuctionFids::FID_CITY, $this->getCity()))->toArray(),
            (new Field(AuctionFids::FID_DESCRIPTION, $this->getDescription()))->toArray(),
            (new Field(AuctionFids::FID_POSTCODE, $this->getPostcode()))->toArray(),
            (new Field(AuctionFids::FID_CONDITION, $this->getCondition()))->toArray(),
            (new Field(AuctionFids::FID_SALE_FORMAT, $this->getSaleFormat()))->toArray(),
            (new Field(AuctionFids::FID_BUY_NOW_PRICE, $this->getBuyNowPrice()))->toArray(),
            (new Field(AuctionFids::FID_SHIPPING_PAID_BY, $this->getShippingPaidBy()))->toArray(),
            (new Field(AuctionFids::FID_POST_PACKAGE_PRIORITY_PRICE, $this->getPostPackagePriorityPrice()))->toArray(),
        ];

        $this->addPhotoFields($fields);

        return [
            'fields' => $fields,
            'localId' => $this->getLocalId(),
        ];
    }


    protected function addPhotoFields(array& $fields)
    {
        $count = count($this->photos);
        if (!$count) {
            return;
        }

        $index = 0;
        foreach ($this->photos as $photo) {
            $fields[] = new Field(AuctionFids::FID_PHOTO + $index, file_get_contents($photo), Field::VALUE_IMAGE);
            $index++;
        }
    }


    public function __call($name, $args)
    {
        if (strpos($name, 'get') === 0) {
            $property = mb_strtolower(substr($name, 3));
            if (property_exists($this, $property)) {
                return $this->{$property};
            } else {
                throw new Exception('Unknown auction property: ' . $property);
            }
        }
    }


}
