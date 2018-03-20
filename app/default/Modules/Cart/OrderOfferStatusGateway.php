<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class OrderOfferStatusGateway extends TableDataGateway
{

    protected static $table = 'order_offer_status';
    protected static $fields = array(
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'decreased' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        'status' => "varchar(32) NOT NULL DEFAULT ''",
        'color' => "varchar(6) NOT NULL DEFAULT ''",
        'title' => "varchar(32) NOT NULL DEFAULT ''",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );
}