<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ListTagTypeGateway extends TableDataGateway
{
    protected static $table = 'list_tag_type';
    protected static $fields = array(
        'tag_type_id' => "int(10) NOT NULL default '0'",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => ['tag_type_id'],
    );

}
