<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class TagTypeGateway extends TableDataGateway
{
    protected static $table = 'tag_type';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'type' => "VARCHAR(255) NOT NULL DEFAULT ''",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY type' => 'type',
    );

}
