<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceFilterTagGateway extends TableDataGateway
{

    protected static $table = 'resource_filter_tag';
    protected static $fields = [
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => ['resource_id', 'tag_id'],
        'KEY tag_id' => 'tag_id',
    ];
    
    public function getTriggers()
    {
        
        $filterTable = FilterTagTypeGateway::instance()->getTable();
        $filterUpdate = $this->prepareFill()->where('resource_tag.tag_type_id = NEW.tag_type_id')->make();
        
        $resourceTagTable = ResourceTagGateway::instance()->getTable();
        $resourceTagUpdate = $this->prepareFill()->where('resource_tag.resource_id = NEW.resource_id AND resource_tag.tag_id = NEW.tag_id')->make();
        
        $resourceTable = ResourceGateway::instance()->getTable();
        
        return [
            [
                $filterTable, 
                'after insert',
                "INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) ".$filterUpdate.";"
            ],
            [
                $filterTable, 
                'after delete',
                'DELETE FROM resource_filter_tag WHERE tag_type_id = OLD.tag_type_id;'
            ],
            [
                $resourceTable,
                'after delete',
                "DELETE FROM resource_filter_tag WHERE resource_id = OLD.id"
            ],
            [
                $resourceTagTable,
                'after insert',
                "INSERT IGNORE INTO resource_filter_tag (resource_id, tag_id, tag_type_id) ".$resourceTagUpdate.";"
            ],
            [
                $resourceTagTable,
                'after delete',
                "DELETE FROM resource_filter_tag WHERE resource_id = OLD.resource_id AND tag_id = OLD.tag_id;"
            ]
        ];
    }
    
    public function prepareFill()
    {
        return ResourceTagGateway::instance()
            ->select('resource_id')
            ->select('tag_id')
            ->select('tag_type_id');
    }
    
    public function getFilterTags($gw)
    {
        
        $tags = $this
            ->innerJoin(
                FilterTagTypeGateway::instance()->on('tag_type_id')
            )
            ->innerJoin(
                $gw
            )
            ->innerJoin(
                TagGateway::instance()
                ->on('id', 'tag_id')
                ->calculate('DISTINCT tag.id')
                ->select('tag')
            )
            ->orderBy('tag')
            ->get();
        
        return $tags;
    }
    
}