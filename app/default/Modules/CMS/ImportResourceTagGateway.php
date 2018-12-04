<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ImportResourceTagGateway extends TableDataGateway
{

    protected static $table = 'import_resource_tag';
    protected static $fields = array(
        'import_id' => "int(11) NOT NULL DEFAULT '0'",
        'resource_id' => "int(11) NOT NULL DEFAULT '0'",
        'tag_id' => "int(11) NOT NULL DEFAULT '0'",
        'tag_type_id' => "INT(11) NOT NULL DEFAULT 0",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => ['import_id', 'resource_id', 'tag_id'],
    );
    
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


}
