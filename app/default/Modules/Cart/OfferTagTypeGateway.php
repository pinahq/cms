<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class OfferTagTypeGateway extends TableDataGateway
{
    protected static $table = 'offer_tag_type';
    protected static $fields = array(
        'tag_type_id' => "INT(10) NOT NULL DEFAULT 0",
    );

    protected static $indexes = array(
        'PRIMARY KEY' => array('tag_type_id')
    );

}
