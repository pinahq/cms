<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;
        /*
            . "'confirmed',"//наличие подтверждено
            . "'substitute-offered',"//предложили замену
            . "'approved',"//согласовано с клиентом
            . "'prepayed',"//предоплата поступила
            . "'for-assembling',"//отправлен на комплектацию
            . "'assembling',"//комплектуется
            . "'assembled',"//укомплектован
            . "'for-delivering',"//передан в службу доставки
            . "'delivering',"//доставляется
            . "'delivering-redirected',"//доставка перенесена
            . "'failed-call',"//недозвон
            . "'out-of-stock',"//нет в наличии
            . "'already-buyed',"//купил в другом месте
            . "'canceled-delivery',"//не устроила доставка
            . "'canceled-price'"//не устроила цена
         */

class OrderStatusGateway extends TableDataGateway
{

    protected static $table = 'order_status';
    protected static $fields = array(
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'group' => "enum('placed','approval','assembling','delivering','complete','cancelled') NOT NULL DEFAULT 'placed'",
        'status' => "varchar(32) NOT NULL DEFAULT ''",
        'color' => "varchar(6) NOT NULL DEFAULT ''",
        'title' => "varchar(32) NOT NULL DEFAULT ''",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );
}