<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

//UPDATE resource_tag INNER JOIN tag using(tag_id) SET resource_tag.tag_type_id = tag.tag_type_id;

class ResourceTagGateway extends TableDataGateway
{

    protected static $table = 'resource_tag';
    protected static $fields = [
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => ['resource_id', 'tag_id'],
        'KEY resource_tag_type_tag' => ['resource_id', 'tag_type_id', 'tag_id'],
        'KEY tag_id_tag_type_id' => ['tag_id','tag_type_id'],
    ];
    
    public function getTriggers()
    {
        return [
            [   
                $this->getTable(), 
                'before insert',
                "SET NEW.tag_type_id=IFNULL((SELECT tag_type_id FROM tag WHERE id=NEW.tag_id LIMIT 1), 0)"
            ],
        ];
    }

    public function edit($resourceId, $tagIds)
    {
        $tagIds = array_unique(array_filter($tagIds));
        $this->whereBy('resource_id', $resourceId)->whereNotBy('tag_id', $tagIds)->delete();
        $toInsert = array();
        foreach ($tagIds as $id) {
            $toInsert[] = array('resource_id' => $resourceId, 'tag_id' => $id);
        }
        $this->insertIgnore($toInsert);
    }

}
