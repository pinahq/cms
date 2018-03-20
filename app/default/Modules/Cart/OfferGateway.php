<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;

class OfferGateway extends TableDataGateway
{
    protected static $table = 'offer';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'sale_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'actual_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'amount' => "INT(10) NOT NULL DEFAULT 0",
        'min_amount' => "INT(10) NOT NULL DEFAULT 1",
        'fold' => "INT(10) NOT NULL DEFAULT 1", 
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        'cost_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'external_id' => "varchar(255) NOT NULL DEFAULT ''",
        'order' => "int(11) UNSIGNED NOT NULL DEFAULT '1'",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'KEY resource_id' => 'resource_id',
    );
    
    public function getTriggers()
    {
        $actualPriceCondition = 'SET NEW.actual_price = IF('
            .'NEW.sale_price > 0 AND NEW.sale_price < NEW.price'.
            ',NEW.sale_price, NEW.price'
            .')';
        return [
            [
                $this->getTable(),
                "before insert",
                $actualPriceCondition
            ],
            [
                $this->getTable(),
                "before update",
                $actualPriceCondition
            ],
            [
                $this->getTable(),
                'after insert',
                " IF (NEW.resource_id > 0 AND NEW.enabled = 'Y') THEN "
                    . $this->getResourcePriceUpdateTrigger('NEW')
                . " END IF;"
                . " IF (NEW.enabled = 'Y') THEN "
                    . $this->getResourceAmountUpdateTrigger('NEW', " + IF(NEW.amount>0,1,0)")
                . " END IF;"
            ],
            [
                $this->getTable(),
                'after update',
                " IF (NEW.resource_id <> OLD.resource_id "
                . " OR NEW.enabled <> OLD.enabled "
                . " OR (NEW.amount > 0 AND OLD.amount = 0)"
                . " OR (NEW.amount = 0 AND OLD.amount > 0)"
                . " OR NEW.price <> OLD.price "
                . " OR NEW.sale_price <> OLD.sale_price "
                . ") THEN "
                    . " IF (OLD.resource_id > 0 AND OLD.enabled = 'Y') THEN "
                        . $this->getResourcePriceUpdateTrigger('OLD')
                    . ' END IF;'
                    . " IF (NEW.resource_id > 0 AND NEW.enabled = 'Y') THEN "
                        . $this->getResourcePriceUpdateTrigger('NEW')
                    . ' END IF;'
                . " END IF;"
                . " IF (NEW.resource_id <> OLD.resource_id "
                . " OR NEW.enabled <> OLD.enabled "
                . " OR NEW.amount <> OLD.amount"
                . " OR (NEW.amount > 0 AND OLD.amount = 0)"
                . " OR (NEW.amount = 0 AND OLD.amount > 0)"
                . ") THEN "
                    . " IF (OLD.enabled = 'Y') THEN "
                        . $this->getResourceAmountUpdateTrigger('OLD', ' - IF(OLD.amount>0,1,0)')
                    . ' END IF;'
                    . " IF (NEW.enabled = 'Y') THEN "
                        . $this->getResourceAmountUpdateTrigger('NEW', ' + IF(NEW.amount>0,1,0)')
                    . ' END IF;'
                . " END IF;"
            ],
            [
                $this->getTable(),
                'before delete',
                 'DELETE FROM offer_tag WHERE offer_id=OLD.id;'
            ],
            [
                $this->getTable(),
                'after delete',
                " IF (OLD.resource_id > 0 AND OLD.enabled = 'Y') THEN "
                    . $this->getResourcePriceUpdateTrigger('OLD')
                . ' END IF;'
                . " IF (OLD.enabled = 'Y') THEN "
                    . $this->getResourceAmountUpdateTrigger('OLD', ' - IF(OLD.amount>0,1,0)')
                . ' END IF;'
            ],
        ];
    }
    
    public function getResourcePriceUpdateTrigger($type)
    {
        return " INSERT INTO resource_price (resource_id, price, sale_price, actual_price)"
                . " SELECT resource_id, price, sale_price, actual_price FROM offer WHERE resource_id = $type.resource_id AND enabled = 'Y' AND amount > 0 ORDER BY actual_price ASC LIMIT 1"
                . " ON DUPLICATE KEY UPDATE price = offer.price, sale_price = offer.sale_price, actual_price = offer.actual_price;";
    }

    public function getResourceAmountUpdateTrigger($type, $cond)
    {
        return "UPDATE resource_amount ra
            INNER JOIN (
                SELECT resource_parent_id FROM resource_tree rt WHERE resource_id = $type.resource_id 
                UNION SELECT $type.resource_id as resource_parent_id
            ) parents ON parents.resource_parent_id = ra.resource_id
            SET ra.amount = ra.amount $cond;";
    }
    
    public function withCartOfferAmount($cartId, $field = 'cart_offer_amount')
    {
        return $this->leftJoin(
            CartOfferGateway::instance()->on('offer_id', 'id')->onBy('cart_id', $cartId)->selectAs('amount', $field)
        );
    }
    
    public function uniqueTags()
    {
        return $this->innerJoin(
            OfferTagGateway::instance()->on('offer_id', 'id')->select('tag_id')->calculate('count(*) as cnt')
        )
        ->groupBy('offer_tag.tag_id')
        ->having('cnt = 1');
    }
    
    public function withConcatUniqueTags($resourceId)
    {
        return $this->leftJoin(
                \Pina\SQL::subquery(
                    OfferGateway::instance()->innerJoin(
                        OfferTagGateway::instance()->on('offer_id', 'id')->select('tag_id')->calculate('count(*) as cnt')
                    )
                    ->whereBy('resource_id', $resourceId)
                    ->groupBy('offer_tag.tag_id')
                    ->having('cnt = 1')
                )->alias('unique_tag')->on('unique_tag.tag_id', 'unique_tag.tag_id')
            )
            ->leftJoin(
                OfferTagGateway::instance()->on('offer_id', 'id')->on('tag_id', 'unique_tag.tag_id')
                    ->leftJoin(
                        TagGateway::instance()->on('id', 'tag_id')->concatTags('tags')
                    )
            );
    }
    
    public function whereTagIds($tagIds)
    {
        if (empty($tagIds) || !is_array($tagIds)) {
            return $this;
        }
        
        foreach ($tagIds as $tagId) {
            $this->innerJoin(
                OfferTagGateway::instance()->alias('tag_'.$tagId)->on('offer_id', 'id')->whereBy('tag_id', $tagId)
            );
        }
        
        return $this;
    }
    
    public function withTags($offerTagTypeGateway = null, $field = 'tags')
    {
        $gw = TagGateway::instance()->on('id', 'tag_id')->concatTags($field);
        
        if (!empty($offerTagTypeGateway)) {
            $this->leftJoin($offerTagTypeGateway);
            $gw->on('tag_type_id', $offerTagTypeGateway->getAlias().'.tag_type_id');
        }
        
        return $this->leftJoin(OfferTagGateway::instance()->on('offer_id', 'id')->leftJoin($gw));
    }

    public function updateByExternalId($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            return;
        }
        
        $q = "UPDATE offer SET amount = (CASE external_id ";
        $ids = [];
        foreach ($data as $externalId => $amount) {
            $q .= ' WHEN '.intval($externalId).' THEN '.intval($amount);
            $ids[] = intval($externalId);
        }
        $q .= ' END)';
        $q .= ' WHERE external_id IN ('.implode(',', $ids).')';
        
        $this->db->query($q);
    }
    
    public function filters($filters)
    {
        if (isset($filters['resource_id'])) {
            $this->whereBy('resource_id', $filters['resource_id']);
        }

        if (isset($filters['price']) && $filters['price'] == 'none') {
            $this->whereBy('actual_price', 0);
        }

        if (isset($filters['stock']) && $filters['stock'] == 'none') {
            $this->whereBy('amount', 0);
        }

        if (isset($filters['stock']) && $filters['stock'] == 'presented') {
            $this->whereNotBy('amount', 0);
        }
        
        return $this;
    }
    
}
