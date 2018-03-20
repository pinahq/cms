<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class PaymentMethodGateway extends TableDataGateway
{
    protected static $table = 'payment_method';
    protected static $fields = [
        'id' => "INT(3) NOT NULL AUTO_INCREMENT",
        'title' => "VARCHAR(100) NOT NULL DEFAULT '0'",
        'resource' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'order' => "INT(11) NOT NULL DEFAULT '0'",
        'enabled' => "VARCHAR(1) NOT NULL DEFAULT 'Y'"
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY resource' => 'resource'
    ];

    public function enabled()
    {
        return $this->whereBy('enabled', 'Y');
    }
}
