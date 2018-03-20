<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class MenuItemGateway extends TableDataGateway
{

    protected static $table = 'menu_item';
    protected static $fields = [
        'id' => "int(11) NOT NULL AUTO_INCREMENT",
        'menu_key' => "varchar(16) NOT NULL DEFAULT ''",
        'title' => "varchar(255) NOT NULL DEFAULT ''",
        'link' => "varchar(255) NOT NULL DEFAULT ''",
        'resource_id' => "int(10) DEFAULT NULL",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        'order' => "int(11) NOT NULL DEFAULT '0'",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY menu_key' => ['menu_key', 'resource_id'],
        'KEY ord' => 'order',
    ];

    public function getTriggers()
    {
        return [
            [
                $this->getTable(),
                'before insert',
                "SET NEW.order=(SELECT IFNULL(MAX(`order`),0)+1 FROM menu_item);"
            ],
        ];
    }

}
