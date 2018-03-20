<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class CartCouponGateway extends TableDataGateway
{

    protected static $table = 'cart_coupon';
    protected static $fields = array(
        'cart_id' => "VARCHAR(36) NOT NULL DEFAULT ''",
        'coupon' => "VARCHAR(32) NOT NULL DEFAULT ''",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'cart_id'
    );
    

}
