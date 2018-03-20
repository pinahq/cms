<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class ShippingFeeGateway extends TableDataGateway
{
    protected static $table = 'shipping_fee';

    protected static $fields = array(
        'shipping_method_id' => "int(11) NOT NULL DEFAULT '0'",
        
        'country_key' => "varchar(2) NOT NULL DEFAULT ''",
        'region_key' => "varchar(6) NOT NULL DEFAULT ''",
        'city_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        
        'fee' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => ['shipping_method_id', 'country_key', 'region_key', 'city_id'],
    );
}
