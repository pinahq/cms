<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ContentGateway extends TableDataGateway
{
    protected static $table = 'content';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'slot' => "VARCHAR(16) NOT NULL DEFAULT ''",//slot_name => slot
        'text' => "TEXT NOT NULL",
        'params' => "TEXT NOT NULL",//json
        'content_type_id' => "INT(10) NOT NULL DEFAULT 0",
        'order' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'KEY resource_slot' => ['resource_id', 'slot', 'order'],
    );

}
