<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class DiscountGateway extends TableDataGateway
{

    protected static $table = 'discount';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'parent_id' => "int(10) NOT NULL DEFAULT '0'",
        'user_tag_id' => "int(10) NOT NULL DEFAULT '0'",
        'resource_tag_id' => "int(10) NOT NULL DEFAULT '0'",
        'percent' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
    );
}