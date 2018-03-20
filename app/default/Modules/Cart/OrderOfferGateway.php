<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\ResourceTagsGateway;

class OrderOfferGateway extends TableDataGateway
{

    protected static $table = 'order_offer';
    protected static $fields = [
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'order_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'offer_id' => "int(11) UNSIGNED NULL DEFAULT NULL",
        
        'amount' => "int(11) NOT NULL DEFAULT '0'",
        'order_offer_status_id' => "int(11) NOT NULL DEFAULT '0'",
        'decreased' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        'actual_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        /* дублирующиеся поля */
        'title' => "varchar(255) NOT NULL DEFAULT ''",
        'image_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'tags' => "varchar(1000) NOT NULL DEFAULT ''",
        'price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'sale_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'cost_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY order_offer' => ['order_id', 'offer_id'],
    ];

    public function getTriggers()
    {
        $setOrderOfferIncreasedById = " SET NEW.decreased = (SELECT order_offer_status.decreased FROM order_offer_status WHERE order_offer_status.id = NEW.order_offer_status_id LIMIT 1);";
        $orderUpdatedCondition = 'UPDATE `order` SET updated = CURRENT_TIMESTAMP WHERE order.id = NEW.order_id;';
        
        return [
            [
                $this->getTable(),
                'before insert',
                " IF (NEW.order_offer_status_id IS NULL OR NEW.order_offer_status_id = 0) THEN "
                    . " BEGIN"
                        //." SET NEW.decreased = (SELECT decreased FROM order_offer_status WHERE order_offer_status = 'placed' LIMIT 1);"
                        //." SET NEW.order_offer_status_id = (SELECT order_offer_status.id FROM order_offer_status WHERE order_offer_status.status = 'placed' LIMIT 1);"
                        . " DECLARE my_order_offer_status_id INT DEFAULT 0;"
                        . " DECLARE my_order_offer_status_decreased enum('Y','N') DEFAULT 'N';"
                        . " SELECT order_offer_status.id, order_offer_status.decreased"
                            . " FROM order_offer_status WHERE status = 'placed'"
                            . " INTO @my_order_offer_status_id, @my_decreased;"
                        . " SET NEW.order_offer_status_id = @my_order_offer_status_id,"
                            . " NEW.decreased = @my_decreased;"
                    . " END;"
                . " ELSE "
                    . $setOrderOfferIncreasedById
                . " END IF;"
            ],
            
            [
                $this->getTable(),
                'before update',
                " IF (NEW.order_offer_status_id IS NOT NULL AND NEW.order_offer_status_id <> 0) THEN "
                    . $setOrderOfferIncreasedById
                . " END IF;"
            ],
            
            [
                $this->getTable(),
                'after insert',
                $this->makeUpdateOrderTrigger('NEW.order_id')
                ." IF (NEW.decreased='Y') THEN UPDATE offer SET offer.amount = offer.amount - NEW.amount WHERE offer.id = NEW.offer_id; END IF;"
            ],
            [
                $this->getTable(),
                'after update',
                $this->makeUpdateOrderTrigger('NEW.order_id') 
                ." IF (OLD.decreased='Y') THEN UPDATE offer SET offer.amount = offer.amount + OLD.amount WHERE offer.id = OLD.offer_id; END IF;"
                ." IF (NEW.decreased='Y') THEN UPDATE offer SET offer.amount = offer.amount - NEW.amount WHERE offer.id = NEW.offer_id; END IF;"
            ],
            [
                $this->getTable(),
                'after delete',
                $this->makeUpdateOrderTrigger('OLD.order_id') 
                ." IF (OLD.decreased='Y') THEN UPDATE offer SET offer.amount = offer.amount + OLD.amount WHERE offer.id = OLD.offer_id; END IF;"
            ],
        ];
    }
   
    private function makeUpdateOrderTrigger($id)
    {
        return "
            UPDATE `order` SET subtotal = (
                SELECT IFNULL(SUM(actual_price * amount),0)
                FROM order_offer WHERE order_offer.order_id = $id AND order_offer.decreased = 'Y'
            ), amount = (
                SELECT IFNULL(SUM(amount),0)
                FROM order_offer WHERE order_offer.order_id = $id AND order_offer.decreased = 'Y'
            ), updated = CURRENT_TIMESTAMP
            WHERE order.id = $id;";
    }
    
    public function addFromCart($orderId, $gw)
    {
        $this->context('order_id', $orderId)->insert($gw->prepareForAdding());
    }

    public function add($orderId, $offerId, $amount)
    {
        $data = OfferGateway::instance()
            ->select('actual_price')->selectAs('id', 'offer_id')->select('price')->select('sale_price')->select('cost_price')
            ->whereId($offerId)
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'resource_id')->select('title')->select('image_id')
            )
            ->leftJoin(
                ResourceTagsGateway::instance()->on('resource_id')->select('tags')
            )
            ->leftJoin(
                OfferTagTypeGateway::instance()
            )
            ->leftJoin(
                OfferTagGateway::instance()->on('offer_id', 'id')
                    ->leftJoin(
                        TagGateway::instance()->on('id', 'tag_id')->on('tag_type_id', 'offer_tag_type.tag_type_id')
                            ->concatTags('offer_tags')
                    )
            )
            ->groupBy('offer.id')
            ->first();
        
        $data['order_id'] = $orderId;
        $data['amount'] = $amount;
        $data['actual_price'] = $data['actual_price'];
        $data['tags'] = $data['resource_tags']."\n".$data['offer_tags'];
        
        $q = $this->makeInsert($data) . " ON DUPLICATE KEY UPDATE `amount` = `amount` + ".intval($amount);

        $this->db->query($q);
    }
    
    public function withStatus()
    {
        return $this->leftJoin(
            OrderOfferStatusGateway::instance()->on('id', 'order_offer_status_id')
                ->selectAs('title', 'order_offer_status_title')
                ->selectAs('status', 'order_offer_status')
                ->selectAs('id', 'order_offer_status_id')
                ->selectAs('color', 'order_offer_status_color')
        );
    }
}
