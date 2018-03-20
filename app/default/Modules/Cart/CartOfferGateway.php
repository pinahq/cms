<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceTagsGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;

class CartOfferGateway extends TableDataGateway
{

    protected static $table = 'cart_offer';
    protected static $fields = array(
        'cart_id' => "VARCHAR(36) NOT NULL DEFAULT ''",
        'user_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'offer_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'resource_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'amount' => "int(11) UNSIGNED NOT NULL",
        'created' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    );
    protected static $indexes = array(
        'PRIMARY KEY' => array('cart_id', 'offer_id'),
    );
    
    public function getTriggers()
    {
        $maxAmountTrigger = "SET NEW.amount = (SELECT LEAST(NEW.amount,offer.amount) FROM offer WHERE offer.id = NEW.offer_id LIMIT 1);";
        return [
            [
                $this->getTable(),
                'before insert',
                $maxAmountTrigger
            ],
            [
                $this->getTable(),
                'before update',
                $maxAmountTrigger
            ],
            [
                $this->getTable(),
                'before insert',
                'SET NEW.resource_id = (SELECT offer.resource_id FROM offer WHERE offer.id = NEW.offer_id)'
            ]
        ];
    }

    public function addOfferAmount($cartId, $offerId, $amount, $userId)
    {
        $cartId = $this->db->escape($cartId);
        $offerId = intval($offerId);
        $amount = intval($amount);
        $userId = intval($userId);
        return $this->db->query("
            INSERT INTO `".$this->getTable()."`
            SET `cart_id` = '".$cartId."',
                `offer_id` = ".$offerId.",
                `amount` = ".$amount.",
                `user_id` = ".$userId."
            ON DUPLICATE KEY UPDATE
            `amount` = `amount` + ".$amount.", user_id=".$userId."
        ");
    }
    
    public function calculateSubtotal()
    {
        return $this->calculate('offer.actual_price * '.$this->getAlias().'.amount as cart_offer_subtotal');
    }
    
    public function calculatedSubtotalValue()
    {
        return $this->db->one(
            $this->calculate('SUM(offer.actual_price * '.$this->getAlias().'.amount) as cart_subtotal')
                ->innerJoin(
                    OfferGateway::instance()->on('id', 'offer_id')
                )
                ->make()
        );
    }
    
    public function withOfferTag()
    {
        return $this->select('offer_id')
            ->leftJoin(
                OfferTagTypeGateway::instance()
            )
            ->leftJoin(
                OfferTagGateway::instance()->on('offer_id')
                    ->leftJoin(
                        TagGateway::instance()->on('id', 'tag_id')->on('tag_type_id', 'offer_tag_type.tag_type_id')
                            ->concatTags('tags')
                    )
            )
            ->groupBy($this->getAlias().'.offer_id');
    }
    
    public function prepareForAdding()
    {
        $cloneGw = clone($this);
        
        $data = $this
            ->select('amount')
            ->innerJoin(
                OfferGateway::instance()->on('id', 'offer_id')->select('actual_price')->selectAs('id', 'offer_id')->select('price')->select('sale_price')->select('cost_price')
            )
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'resource_id')->select('title')->select('image_id')
            )
            ->leftJoin(
                ResourceTagsGateway::instance()->on('resource_id')->selectAs('tags', 'resource_tags')
            )
            ->leftJoin(
                \Pina\SQL::subquery(
                    $cloneGw->withOfferTag()
                )->alias('offer_tag')->on('offer_id')->selectAs('tags', 'offer_tags')
            )
            ->groupBy('cart_offer.offer_id')
            ->get();
        
        foreach ($data as $k => $item) {
            $data[$k]['tags'] = $data[$k]['resource_tags']."\n".$data[$k]['offer_tags'];
        }
        
        return $data;
    }


}
