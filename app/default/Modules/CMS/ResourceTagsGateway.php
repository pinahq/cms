<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceTagsGateway extends TableDataGateway
{

    protected static $table = 'resource_tags';
    protected static $fields = [
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'tags' => "VARCHAR(1000) NULL",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'resource_id'
    ];
    
    public function getTriggers()
    {
        $fullGw = ResourceGateway::instance()
                ->select('id')
                ->withTags(ListTagTypeGateway::instance())
                ->groupBy('resource.id');
               
        $gw = clone($fullGw);
        $listUpdate = $gw->innerJoin(
            \Pina\SQL::subquery(
                ResourceTagGateway::instance()
                    ->calculate('DISTINCT resource_id')
                    ->where('resource_tag.tag_type_id = NEW.tag_type_id')
            )->alias('search')->on('resource_id', 'id')
        )->make();
        $gw = clone($fullGw);
        $newUpdate = $gw->where('resource.id = NEW.resource_id')->make();
        $gw = clone($fullGw);
        $oldUpdate = $gw->where('resource.id = OLD.resource_id')->make();
        
        $listTable = ListTagTypeGateway::instance()->getTable();
        $resourceTable = ResourceGateway::instance()->getTable();
        $resourceTagTable = ResourceTagGateway::instance()->getTable();
        
        return [
            [
                $listTable, 
                'after insert',
                "REPLACE INTO resource_tags (resource_id, tags) ".$listUpdate.";"
            ],
            [
                $resourceTable,
                'after delete',
                "DELETE FROM resource_tags WHERE resource_id = OLD.id"
            ],
            [
                $resourceTagTable,
                'after insert',
                "REPLACE INTO resource_tags (resource_id, tags) ".$newUpdate.";"
            ],
            [
                $resourceTagTable,
                'after delete',
                "REPLACE INTO resource_tags (resource_id, tags) ".$oldUpdate.";"
            ]
        ];
    }
    
}