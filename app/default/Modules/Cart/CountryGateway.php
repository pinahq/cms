<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;

class CountryGateway extends TableDataGateway
{
    protected static $table = 'country';
    protected static $fields = array(
        'key' => "varchar(2) NOT NULL DEFAULT ''",
        'country' => "varchar(128) NOT NULL DEFAULT ''",
        'importance' => "int(11) NOT NULL DEFAULT '0'",//важные страны дублируются в начале выпадающего списка
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'key'
    );
}