<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;
use Pina\Modules\Images\ImageGateway;

//update resource r1 inner join resource r2 on r2.resource=r1.resource and r2.id<>r1.id set r1.resource = UUID();
//update resource r inner join resource_tree rt on rt.resource_id=r.id and rt.length=1 set r.order=rt.resource_order;

class ResourceGateway extends TableDataGateway
{

    protected static $table = 'resource';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'parent_id' => "int(10) NOT NULL DEFAULT 0", //основной родитель в иерархии, см. ResourceTreeGateway
        'resource' => "varchar(255) NOT NULL DEFAULT ''",
        'title' => "varchar(255) NOT NULL DEFAULT ''",
        'resource_type_id' => "int(10) NOT NULL DEFAULT 0",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        'image_id' => "int(10) NOT NULL DEFAULT 0", //обратная ссылка на основную картинку
        'content_id' => "int(10) NOT NULL DEFAULT 0", //обратная ссылка на превью контента
        'external_id' => "varchar(255) NOT NULL DEFAULT ''",
        'order' => "INT(10) NOT NULL DEFAULT 0",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY resource' => 'resource',
        'KEY parent_id' => 'parent_id',
        'KEY enabled' => 'enabled',
        'KEY ord' => 'order',
        'KEY resource_type_enabled' => ['resource_type_id', 'enabled'],
        'FULLTEXT title' => 'title'
    );

    public function getTriggers()
    {
        $addTreeNode = "
            IF (NEW.parent_id > 0) THEN
                INSERT INTO resource_tree (resource_parent_id, resource_id, length, resource_type_id, resource_enabled, resource_order)
                SELECT resource_tree.resource_parent_id, NEW.id, resource_tree.length + 1, NEW.resource_type_id, NEW.enabled, NEW.order
                FROM resource_tree WHERE resource_id = NEW.parent_id
                UNION
                SELECT NEW.parent_id, NEW.id, 1, NEW.resource_type_id, NEW.enabled, NEW.order;
            ELSE
                INSERT INTO resource_tree (resource_parent_id, resource_id, length, resource_type_id, resource_enabled, resource_order)
                SELECT NEW.parent_id, NEW.id, 1, NEW.resource_type_id, NEW.enabled, NEW.order;
            END IF;";

        return [
            [   
                $this->getTable(), 
                'before insert',
                "SET NEW.resource=IF(NEW.resource IS NULL OR NEW.resource='',UUID(),NEW.resource),"
                . "NEW.order=(SELECT IFNULL(MAX(`order`),0)+1 FROM resource);"
            ],
            [
                $this->getTable(),
                'after insert',
                "
                    IF (NEW.id > 0) THEN $addTreeNode END IF;
                   
                    INSERT INTO resource_url SET resource_id = NEW.id, resource_type_id = NEW.resource_type_id, resource_enabled = NEW.enabled, url = (
                        SELECT concat(IFNULL(concat(group_concat(rp.resource ORDER BY rt.length DESC SEPARATOR '/'), '/'),''), r.resource)
                            from resource r 
                            left join resource_tree rt on rt.resource_id = r.id
                            left join resource rp on rp.id = rt.resource_parent_id
                            WHERE r.id = NEW.id
                            GROUP BY r.id
                            LIMIT 1
                    );
                ",
            ],

            [   
                $this->getTable(), 
                'before update',
                "SET NEW.resource=IF(NEW.resource IS NULL OR NEW.resource='',UUID(),NEW.resource)"
            ],            
            [
                $this->getTable(),
                'after update',
                "
                    IF (OLD.parent_id <> NEW.parent_id) THEN
                        IF (OLD.id > 0) THEN
                            DELETE `t1` FROM resource_tree as t1
                            JOIN resource_tree t2
                                ON t1.resource_id = t2.resource_id 
                                AND t2.resource_parent_id = OLD.id
                            JOIN resource_tree t3 
                                ON t1.resource_parent_id = t3.resource_parent_id
                                AND t3.resource_id = OLD.id;
                            DELETE FROM resource_tree WHERE resource_id = OLD.id;
                        END IF;

                        IF (NEW.id > 0) THEN
                            $addTreeNode

                            INSERT INTO resource_tree (resource_parent_id, resource_id, length, resource_type_id, resource_enabled, resource_order)
                            SELECT t1.resource_parent_id, t2.resource_id, t1.length + t2.length, t2.resource_type_id, t2.resource_enabled, t2.resource_order
                            FROM resource_tree t1 CROSS JOIN resource_tree t2
                            WHERE t1.resource_id = NEW.id AND t2.resource_parent_id = NEW.id;
                        END IF;
                    ELSEIF (OLD.enabled <> NEW.enabled OR OLD.order <> NEW.order) THEN
                            UPDATE resource_tree SET resource_enabled = NEW.enabled, resource_order=NEW.order WHERE resource_id = NEW.id;
                            UPDATE resource_url SET resource_enabled = NEW.enabled WHERE resource_id = NEW.id;
                    END IF;

                    IF (OLD.resource <> NEW.resource OR OLD.parent_id <> NEW.parent_id) THEN

                        UPDATE resource_url ru
                        inner join (
                            SELECT resource_id FROM resource_tree WHERE resource_parent_id = NEW.id 
                            UNION 
                            select NEW.id as resource_id
                        ) as rt_link on rt_link.resource_id = ru.resource_id
                        inner join (
                            SELECT r.id, concat(IFNULL(concat(group_concat(rp.resource ORDER BY rt.length DESC SEPARATOR '/'), '/'),''), r.resource) as url
                            from resource r
                            inner join (SELECT resource_tree.resource_id FROM resource_tree WHERE resource_tree.resource_parent_id = NEW.id UNION select NEW.id as resource_id) as rt_link on rt_link.resource_id = r.id
                            left join resource_tree rt on rt.resource_id = r.id
                            left join resource rp on rp.id = rt.resource_parent_id
                            GROUP BY r.id
                        ) d on d.id = ru.resource_id
                        SET ru.url = d.url;

                    END IF;
                "
            ],
            [
                $this->getTable(),
                'after delete',
                ' 
                    IF (OLD.id > 0) THEN
                        DELETE t1 FROM resource_tree t1
                        JOIN resource_tree t2
                            ON t1.resource_id = t2.resource_id 
                            AND t2.resource_parent_id = OLD.id
                        JOIN resource_tree t3 
                            ON t1.resource_parent_id = t3.resource_parent_id
                            AND t3.resource_id = OLD.id;
                        DELETE FROM resource_tree WHERE resource_id = OLD.id;
                        DELETE FROM resource_tree WHERE resource_parent_id = OLD.id;
                        DELETE FROM resource_url WHERE resource_id = OLD.id;
                        DELETE FROM resource_tag WHERE resource_id = OLD.id;
                        DELETE FROM tag WHERE resource_id = OLD.id;
                        DELETE FROM resource_text WHERE resource_id = OLD.id;
                        DELETE FROM resource_meta WHERE resource_id = OLD.id;
                    END IF;
                '
            ],
        ];
    }

    public function whereEnabled()
    {
        return $this->whereBy('enabled', 'Y');
    }

    public function whereResourceType($resourceType)
    {
        if (empty($resourceType)) {
            return $this;
        }
        return $this->innerJoin(
            ResourceTypeGateway::instance()->on('id', 'resource_type_id')->whereBy('type', $resourceType)
        );
    }

    public function withResourceType($patternField = 'pattern')
    {
        return $this->innerJoin(
            ResourceTypeGateway::instance()->on('id', 'resource_type_id')
                ->selectAs('type', 'resource_type')
                ->selectAs('title', 'resource_type_title')
                ->selectAs('tree', 'resource_type_tree')
                ->selectAs($patternField, 'resource_type_pattern')
        );
    }

    public function whereTreeStructured()
    {
        return $this->innerJoin(
            ResourceTypeGateway::instance()->on('id', 'resource_type_id')->onBy('tree', 'Y')
        );
    }

    public function withResourceText()
    {
        return $this->leftJoin(
            ResourceTextGateway::instance()->on('resource_id', 'id')->select('text')
        );
    }
    
    public function withResourceMeta()
    {
        return $this->leftJoin(
            ResourceMetaGateway::instance()->on('resource_id', 'id')
                ->selectAs('title', 'meta_title')
                ->selectAs('description', 'meta_description')
                ->selectAs('keywords', 'meta_keywords')
        );
    }

    public function withImage()
    {
        return $this->leftJoin(
            ImageGateway::instance()->on('id', 'image_id')
                ->selectAs('id', 'image_id')
                ->selectAs('original_id', 'image_original_id')
                ->selectAs('hash', 'image_hash')
                ->selectAs('filename', 'image_filename')
                ->selectAs('url', 'image_url')
                ->selectAs('width', 'image_width')
                ->selectAs('height', 'image_height')
                ->selectAs('type', 'image_type')
                ->selectAs('size', 'image_size')
                ->selectAs('alt', 'image_alt')
        );
    }

    public function withImages()
    {
        return $this->leftJoin(
            ResourceImageGateway::instance()->on('resource_id', 'id')
                ->leftJoin(
                    ImageGateway::instance()->on('id', 'image_id')
                        ->selectAs('original_id', 'image_original_id')
                        ->selectAs('hash', 'image_hash')
                        ->selectAs('filename', 'image_filename')
                        ->selectAs('url', 'image_url')
                        ->selectAs('width', 'image_width')
                        ->selectAs('height', 'image_height')
                        ->selectAs('type', 'image_type')
                        ->selectAs('size', 'image_size')
                        ->selectAs('alt', 'image_alt')
                )
        );
    }

    public function withChildCount()
    {
        return $this->leftJoin(
            ResourceTreeGateway::instance()->alias('child_count')->on('resource_parent_id', 'id')
        )
        ->groupBy('resource.id')
        ->calculate('count(child_count.resource_id) as child_count');
    }

    public function withUrl()
    {
        return $this->innerJoin(
            ResourceUrlGateway::instance()->on('resource_id', 'id')->select('url')
        );
    }
    
    public function withListTags()
    {
        return $this->leftJoin(
            ResourceTagsGateway::instance()->on('resource_id', 'id')->select('tags')
        );
    }

    public function withTags($resourceTypeTagTypeGateway = null)
    {
        $gw = TagGateway::instance()->on('id', 'tag_id')->concatTags('tags');

        if (!empty($resourceTypeTagTypeGateway)) {
            $this->leftJoin($resourceTypeTagTypeGateway);
            $gw->on('tag_type_id', $resourceTypeTagTypeGateway->getAlias().'.tag_type_id');
        }

        return $this->leftJoin(ResourceTagGateway::instance()->on('resource_id', 'id')->leftJoin($gw));
    }

    public function whereChildren($parentId, $length = 0, $resourceTypeId = 0)
    {
        $parentId = intval($parentId);
        $length = intval($length);
        
        $resourceTreeGateway = ResourceTreeGateway::instance()->on('resource_id', 'id')
                ->onBy('resource_parent_id', $parentId);

        if ($length) {
            $resourceTreeGateway->onBy('length', range(1, $length));
        }
        
        if ($resourceTypeId) {
            $resourceTreeGateway->onBy('resource_type_id', $resourceTypeId);
        }

        return $this->innerJoin($resourceTreeGateway);
    }

    public function whereParents($resourceId)
    {
        return $this->innerJoin(
            ResourceTreeGateway::instance()
                ->on('resource_parent_id', 'id')
                ->whereBy('resource_id', $resourceId)
        );
    }

    public function whereTagIds($tagIds)
    {
        if (!is_array($tagIds)) {
            $tagIds = [$tagIds];
        }

        $tagIds = array_unique(array_filter($tagIds));

        if (empty($tagIds)) {
            return $this;
        }

        foreach ($tagIds as $tagId) {
            $this->innerJoin(
                ResourceTagGateway::instance()->alias('tag_' . $tagId)->on('resource_id', 'id')->onBy('tag_id', $tagId)
            );
        }

        return $this;
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
                ResourceFilterTagGateway::instance()->alias('tag_type_' . $tagTypeId)->on('resource_id', 'id')->onBy('tag_id', $tagIds)
            );
        }

        return $this;
    }

    
    public function getTags($resourceType)
    {
        
        $tagTypeIds = FilterTagTypeGateway::instance()
            ->innerJoin(
                ResourceTypeGateway::instance()->on('id', 'resource_type_id')->onBy('type', $resourceType)
            )
            ->column('tag_type_id');
        
        
        return $this->resetSelect()->innerJoin(
            ResourceTagGateway::instance()
            ->on('resource_id', 'id')
            ->innerJoin(
                TagGateway::instance()
                ->on('id', 'tag_id')
                ->calculate('DISTINCT tag.id')
                ->select('tag')
                ->whereBy('tag_type_id', $tagTypeIds)
                ->innerJoin(
                    TagTypeGateway::instance()
                    ->on('id', 'tag_type_id')
                    ->select('type')
                )
            )
        )
        ->orderBy('tag')
        ->get();
    }
    
    public function treeView($resourceId = 0)
    {
        $resourceTypeIds = ResourceTypeGateway::instance()->whereBy('tree', 'Y')->column('id');
        
        return $this
            ->alias('r')
            ->whereBy('resource_type_id', $resourceTypeIds)
            ->select('id')
            ->calculate("concat(IFNULL(concat(group_concat(rp.title ORDER BY rt.length DESC SEPARATOR '/'), '/'),''), r.title) as title")
            ->innerJoin(
                \Pina\SQL::subquery(
                    ResourceTreeGateway::instance()->whereBy('resource_parent_id', $resourceId)->whereBy('resource_type_id', $resourceTypeIds)
                )->alias('rt_link')->on('resource_id', 'id')
            )
            ->leftJoin(
                ResourceTreeGateway::instance()->alias('rt')->on('resource_id', 'id')
                    ->leftJoin(
                        ResourceGateway::instance()->alias('rp')->on('id', 'resource_parent_id')
                    )
            )
            ->groupBy('r.id');
    }
    
    public function findList($resourceId = 0, $excludeParentId = null, $paging = null, $search = '')
    {
        $resourceId = intval($resourceId);
        
        $gw = $this->treeView($resourceId)->orderBy('title');

        if ($excludeParentId) {
            $gw->whereNotBy('id', $excludeParentId);
            $gw->leftJoin(
                ResourceTreeGateway::instance()->alias('exclude')
                    ->on('resource_id', 'id')
                    ->onBy('resource_parent_id', $excludeParentId)
                    ->onBy('resource_type_id', $resourceTypeIds)
                    ->whereNull('resource_id')
            );
        }
        
        if ($search) {
            $gw->having("title LIKE '%".$search."%'");
        }
        
        if ($paging) {
            $gw->paging($paging);
        }
        
        $rs = $gw->get();
        
        return $rs;
    }

    private function createTitle($resources, $resourceId)
    {
        $titles = array();

        foreach ($resources as $r) {
            if ($r['id'] == $resourceId &&
                    $r['parent_title'] != '') {
                array_push($titles, $r['parent_title']);
            }
        }

        return $titles;
    }

}
