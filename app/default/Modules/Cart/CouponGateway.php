<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class CouponGateway extends TableDataGateway
{

    protected static $table = 'coupon';
    protected static $fields = array(
        'coupon' => "VARCHAR(32) NOT NULL DEFAULT ''",
        'absolute' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'percent' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => ['coupon'],
    );
}