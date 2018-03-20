<?php

namespace Pina\Modules\PayPal;

use Pina\TableDataGateway;

class PayPalTokenGateway extends TableDataGateway
{

    protected static $table = 'paypal_token';
    protected static $fields = [
        'payment_id' => "INT(11) NOT NULL DEFAULT '0'",
        'token' => "varchar(128) NOT NULL DEFAULT ''",
        'created' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ];
    protected static $indexes = [
        'PRIMARY KEY' => ['payment_id', 'token'],
    ];

}