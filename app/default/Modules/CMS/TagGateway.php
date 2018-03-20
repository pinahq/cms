<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

/*
  UPDATE tag t
  INNER JOIN tag_type tt ON TRIM(LEFT(t.tag,LOCATE(':',t.tag)-1)) = tt.type
  SET t.tag_type_id = tt.id
  WHERE t.tag_type_id = 0;
 */

class TagGateway extends TableDataGateway
{

    protected static $table = 'tag';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'tag' => "VARCHAR(512) NOT NULL DEFAULT ''",
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'order' => "INT(10) NOT NULL DEFAULT 0",
    );
    /*
     * на случай, если по какой-то причине нужно запустить на версии mysql ниже 5.7,
     * где не поддерживаются полнотекстовые индексы для InnoDB
    protected static $engine = "ENGINE=MyISAM DEFAULT CHARSET=utf8";
     */
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'FULLTEXT tag' => 'tag',
        'KEY tag_type_id' => 'tag_type_id',
        'KEY resource_id' => 'resource_id',
        'KEY ord' => 'order',
    );

    public function getTriggers()
    {
        return [
            [
                $this->getTable(),
                'before insert',
                "
                    BEGIN
                    DECLARE type VARCHAR(255);
                    SET NEW.tag := TRIM(NEW.tag);
                    IF (NEW.tag_type_id = 0) THEN
                        SET @type = TRIM(LEFT(NEW.tag,LOCATE(':',NEW.tag)-1));
                        IF (LENGTH(@type)) THEN
                            INSERT IGNORE INTO tag_type SET type = @type;
                            SET NEW.tag_type_id = (SELECT tag_type.id FROM tag_type WHERE tag_type.type = @type LIMIT 1);
                        END IF;
                    END IF;
                    SET NEW.order=(SELECT IFNULL(MAX(`order`),0)+1 FROM tag);
                    END;
                "
            ],
        ];
    }
    
    public function whereResource()
    {
        return $this->where($this->makeByCondition(array('>', self::SQL_OPERAND_FIELD, 'resource_id', self::SQL_OPERAND_VALUE, 0)));
    }

    public function getIdOrAdd($tag)
    {
        $id = $this->whereBy('tag', $tag)->id();
        if (empty($id)) {
            $id = $this->insertGetId(['tag' => $tag]);
        }
        return $id;
    }

    public function concatTags($name)
    {
        return $this->calculate("GROUP_CONCAT(".$this->getAlias().".tag ORDER BY ".$this->getAlias().".tag ASC SEPARATOR '\n') as " . $name);
    }

    public function withResourceUrl()
    {
        return $this->leftJoin(
            ResourceUrlGateway::instance()->on('resource_id')->select('url')
        );
    }

}
