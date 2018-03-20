<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

//для организации иерархий, например рубрикаций, где рубрика - родительская категория
//DELETE FROM resource_tree WHERE tag_id <> 0;

//UPDATE resource_tree rt INNER JOIN resource r on r.id=rt.resource_id SET rt.resource_type_id=r.resource_type_id, rt.resource_enabled=r.enabled;
class ResourceTreeGateway extends TableDataGateway
{
    protected static $table = 'resource_tree';
    protected static $fields = array(
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'resource_parent_id' => "INT(10) NOT NULL DEFAULT 0",
        'length' => "int(11) NOT NULL DEFAULT '0'",
        'resource_order' => "int(11) NOT NULL AUTO_INCREMENT",
        'resource_type_id' => "int(11) NOT NULL DEFAULT '0'",
        'resource_enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => ['resource_id', 'resource_parent_id'],
        'KEY resource_order' => 'resource_order',
        'KEY parent_enabled_order' => ['resource_parent_id', 'length', 'resource_enabled', 'resource_order'],
        'KEY length' => ['resource_parent_id', 'length', 'resource_type_id', 'resource_order'],
        'KEY resource_tree_parent' => ['resource_parent_id', 'resource_type_id', 'resource_order'],
        'KEY resource_tree_parent_enabled' => ['resource_parent_id', 'resource_type_id', 'resource_enabled', 'resource_order'],
    );

    public function findChildIds($id, $level = 0)
    {
        if (is_array($id)) {
            $id = array_map('intval', $id);
        } else {
            $id = intval($id);
            if ($id == 0) {
                return false;
            }
        }

        return $this->whereBy('resource_parent_id', $id)->whereLevel($level)->column('resource_id');
    }
    
    public function whereLevel($level = 0)
    {
        $level = intval($level);
        if (empty($level)) {
            return $this;
        }
        
        return $this->whereBy('length', $level);
    }


    public function whereFilterTagIds($tagIds, &$needGroupBy)
    {
        if (!is_array($tagIds)) {
            $tagIds = [$tagIds];
        }

        $tagIds = array_unique(array_filter($tagIds));

        if (empty($tagIds)) {
            return $this;
        }
        
        $tags = TagGateway::instance()->whereId($tagIds)->select('id')->select('tag_type_id')->get();
        $tagGroups = \Pina\Arr::groupColumn($tags, 'tag_type_id', 'id');

        foreach ($tagGroups as $tagTypeId => $tagIds) {
            if (is_array($tagIds) && count($tagIds) > 1) {
                $needGroupBy = true;
            }
            $this->innerJoin(
                ResourceFilterTagGateway::instance()->alias('tag_type_' . $tagTypeId)->on('resource_id')->whereBy('tag_id', $tagIds)
            );
        }

        return $this;
    }

}
