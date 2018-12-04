<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceTypeGateway extends TableDataGateway
{
    protected static $table = 'resource_type';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'type' => "VARCHAR(32) NOT NULL DEFAULT ''",
        'title' => "VARCHAR(32) NOT NULL DEFAULT ''",
        "tree" => "enum('Y','N') NOT NULL DEFAULT 'N'",
        'pattern' => "VARCHAR(128) NOT NULL DEFAULT ''",
        'cp_pattern' => "VARCHAR(128) NOT NULL DEFAULT ''",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY title' => 'title',
        'KEY type' => 'type',
    );

}
