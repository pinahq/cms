<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\FilterTagTypeGateway;
use Pina\Modules\CMS\ResourceFilterTagGateway;

//UPDATE offer_tag JOIN tag ON tag.tag_id=offer_tag.tag_id SET offer_tag.tag_type_id=tag.tag_type_id

/*
TRUNCATE resource_filter_tag;
INSERT IGNORE INTO resource_filter_tag (resource_id, resource_type_id, tag_id, tag_type_id) SELECT resource.resource_id, resource.resource_type_id, tag.tag_id, tag.tag_type_id FROM resource INNER JOIN offer ON offer.resource_id = resource.resource_id INNER JOIN offer_tag ON offer_tag.offer_id = offer.id INNER JOIN tag ON tag.tag_id = offer_tag.tag_id INNER JOIN resource_type_filter_tag_type ON resource_type_filter_tag_type.resource_type_id = resource.resource_type_id AND resource_type_filter_tag_type.tag_type_id = tag.tag_type_id WHERE (offer.amount > 0) AND (offer.enabled = 'Y');
*/

class OfferTagGateway extends TableDataGateway
{
    protected static $table = 'offer_tag';
    protected static $fields = array(
        'offer_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => array('offer_id', 'tag_id')
    );
    
    public function getTriggers()
    {
        $fullGw = OfferGateway::instance()
            ->where('offer.amount > 0')
            ->whereBy('enabled', 'Y')
            ->select('resource_id')
            ->innerJoin(
                OfferTagGateway::instance()->on('offer_id', 'id')
                ->innerJoin(
                    FilterTagTypeGateway::instance()->on('tag_type_id')
                )
                ->innerJoin(
                    TagGateway::instance()->on('id', 'tag_id')
                    ->selectAs('id', 'tag_id')
                    ->select('tag_type_id')
                )
            );
        
        $gw = clone($fullGw);
        $filterUpdate = $gw->where('tag.tag_type_id = NEW.tag_type_id')->make();
        
        $gw = clone($fullGw);
        $offerTagUpdate = $gw->where('offer_tag.offer_id = NEW.offer_id AND offer_tag.tag_id = NEW.tag_id')->make();
        
        $gw = clone($fullGw);
        $gw->where('offer.resource_id = @resource_id');
        $offerTagOnDeleteUpdate = $gw->make();
       
        
        $gw = clone($fullGw);
        $offerUpdate = $gw->where('offer.resource_id = NEW.resource_id')->make();
        
        $filterTable = FilterTagTypeGateway::instance()->getTable();
        $offerTable = OfferGateway::instance()->getTable();
        
        return [
            [
                $this->getTable(),
                'before insert',
                "SET NEW.tag_type_id=(SELECT tag_type_id FROM tag WHERE id=NEW.tag_id LIMIT 1)"
            ],
            [
                $filterTable, 
                'after insert',
                'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '.$filterUpdate.';'
            ],
            [
                $this->getTable(),
                'after insert',
                'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '.$offerTagUpdate.';'
            ],
            [
                $this->getTable(),
                'after delete',
                'SET @resource_id = (SELECT resource_id FROM offer WHERE offer.id = OLD.offer_id LIMIT 1);'
                . 'DELETE FROM resource_filter_tag WHERE resource_id = @resource_id;'
                . 'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '.$offerTagOnDeleteUpdate.';'
                . 'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '
                    . ResourceFilterTagGateway::instance()
                        ->prepareFill()
                        ->where('resource_tag.resource_id = @resource_id')
                        ->make().';'
            ],
            [
                $offerTable,
                'after update',
                "IF (NEW.amount > 0 AND OLD.amount = 0) OR (NEW.amount = 0 AND OLD.amount > 0) OR (NEW.enabled <> OLD.enabled) THEN "
                    . "IF (NEW.amount > 0 AND NEW.enabled = 'Y') THEN "
                        . 'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '.$offerUpdate.';'
                    . ' ELSE '
                        . 'DELETE FROM resource_filter_tag WHERE resource_id = NEW.resource_id;'
                        . 'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '.$offerUpdate.';'
                        . 'INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) '
                            . ResourceFilterTagGateway::instance()
                                ->prepareFill()
                                ->where('resource_tag.resource_id = NEW.resource_id')
                                ->make().';'
                
                    . 'END IF;'
                . 'END IF;'
            ]
        ];
    }
    
    public function edit($offerId, $tagIds)
    {
        $tagIds = array_unique(array_filter($tagIds));
        $this->whereBy('offer_id', $offerId)->whereNotBy('tag_id', $tagIds)->delete();
        $toInsert = array();
        foreach ($tagIds as $id) {
            $toInsert[] = array('offer_id' => $offerId, 'tag_id' => $id);
        }
        $this->insertIgnore($toInsert);
    }

}
