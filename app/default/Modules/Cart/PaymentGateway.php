<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class PaymentGateway extends TableDataGateway
{

    protected static $table = 'payment';
    protected static $fields = [
        'id' => "INT(11) NOT NULL AUTO_INCREMENT",
        'payment_method_id' => "INT(11) NOT NULL DEFAULT '0'",
        'order_id' => "INT(11) NOT NULL DEFAULT '0'",
        'status' => "ENUM('new','processed','payed','canceled','failed') NOT NULL DEFAULT 'new'",
        'total' => "DECIMAL(12,2) NOT NULL DEFAULT '0.00'",
        'return_url' => "varchar(128) NOT NULL DEFAULT ''",
        'created' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
        'updated' => 'TIMESTAMP NULL',
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'id',
        'KEY payment_method_id' => 'payment_method_id',
        'KEY order_id' => 'order_id'
    ];

    public function getTriggers()
    {
        return [
                [
                $this->getTable(),
                'BEFORE UPDATE',
                "
                    SET NEW.`updated` = CURRENT_TIMESTAMP;
                    IF NEW.`status` = 'payed' AND OLD.`status` != 'payed' THEN 
                        UPDATE `order` SET `payed` = `payed` + OLD.`total` WHERE `order`.`id` = OLD.`order_id`;
                    END IF;
                    IF NEW.`status` != 'payed' AND OLD.`status` = 'payed' THEN 
                        UPDATE `order` SET `payed` = `payed` - OLD.`total` WHERE `order`.`id` = OLD.`order_id`;
                    END IF;
                "
            ],
                [
                $this->getTable(),
                'BEFORE DELETE',
                'UPDATE `order` SET `payed` = `payed` - OLD.`total` WHERE `order`.`id` = OLD.`order_id`;'
            ]
        ];
    }

    public function whereValid($resource, $status)
    {
        return $this->innerJoin(
                    PaymentMethodGateway::instance()
                    ->on('id', 'payment_method_id')
                    ->enabled()
                    ->whereBy('resource', $resource)
                )
                ->whereBy('status', $status);
    }

}
