<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class OrderOfferTagTypeGateway extends TableDataGateway
{
    protected static $table = 'order_offer_tag_type';
    protected static $fields = array(
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => array('tag_type_id')
    );

}
