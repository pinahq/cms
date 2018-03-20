<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ConfigGateway extends TableDataGateway
{
    protected static $table = "config";
    protected static $fields = [
        'namespace' => "varchar(255) NOT NULL DEFAULT ''",
        'group' => "varchar(32) NOT NULL DEFAULT ''",
        'key' => "varchar(32) NOT NULL DEFAULT ''",
        'value' => "text DEFAULT NULL",
        'type' => "enum('text','textarea','select','checkbox','image') NOT NULL DEFAULT 'text'",
        'variants' => "varchar(1000) NOT NULL DEFAULT ''",
        'title' => "varchar(255) NOT NULL DEFAULT ''",
        'resource' => "varchar(255) NOT NULL DEFAULT ''",
        'order' => "int(1) NOT NULL DEFAULT '0'"
    ];

    protected static $indexes = [
        'PRIMARY KEY' => ['namespace', 'key'],
        'UNIQUE KEY group_key' => ['namespace', 'group', 'key']
    ];
    
    public function whereNamespace($ns) {
        return $this->whereBy('namespace', $ns);
    }

}
