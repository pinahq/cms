<?php

namespace Pina\Modules\Regions;

use Pina\TableDataGateway;

class CityGateway extends TableDataGateway
{
    protected static $table = 'city';
    protected static $fields = array(
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'country_key' => "varchar(2) NOT NULL DEFAULT ''",
        'region_key' => "varchar(6) NOT NULL DEFAULT ''",
        'city' => "varchar(128) NOT NULL DEFAULT ''",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );
}