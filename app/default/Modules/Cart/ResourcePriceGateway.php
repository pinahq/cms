<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class ResourcePriceGateway extends TableDataGateway
{
    protected static $table = 'resource_price';
    protected static $fields = array(
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'sale_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'actual_price' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'resource_id',
    );

}
