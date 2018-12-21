<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class UserTagGateway extends TableDataGateway
{

    protected static $table = 'user_tag';
    protected static $fields = [
        'user_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_id' => "INT(10) NOT NULL DEFAULT 0",
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => ['user_id', 'tag_id'],
    ];

    public function getTriggers()
    {
        return [
            [   
                $this->getTable(), 
                'before insert',
                "SET NEW.tag_type_id=(SELECT tag_type_id FROM tag WHERE id=NEW.tag_id LIMIT 1)"
            ],
        ];
    }

    public function edit($userId, $tagIds)
    {
        $tagIds = array_unique(array_filter($tagIds));
        $this->whereBy('user_id', $userId)->whereNotBy('tag_id', $tagIds)->delete();
        $toInsert = array();
        foreach ($tagIds as $id) {
            $toInsert[] = array('user_id' => $userId, 'tag_id' => $id);
        }
        $this->insertIgnore($toInsert);
    }

}
