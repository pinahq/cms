<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class ShippingMethodGateway extends TableDataGateway
{

    protected static $table = 'shipping_method';
    protected static $fields = array(
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'title' => "varchar(255) NOT NULL DEFAULT ''",
        'description' => "varchar(255) NOT NULL DEFAULT ''",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'N'",
        'order' => "int(11) NOT NULL DEFAULT '0'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );

}
