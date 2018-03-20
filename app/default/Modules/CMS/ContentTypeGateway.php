<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ContentTypeGateway extends TableDataGateway
{
    protected static $table = 'content_type';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'type' => "varchar(32) NOT NULL DEFAULT ''",
        'title' => "VARCHAR(32) NOT NULL DEFAULT ''",
        'module_id' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY type' => 'type'
    );

}
