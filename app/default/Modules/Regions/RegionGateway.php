<?php

namespace Pina\Modules\Regions;

use Pina\TableDataGateway;

class RegionGateway extends TableDataGateway
{
    protected static $table = 'region';
    protected static $fields = array(
        'country_key' => "varchar(2) NOT NULL DEFAULT ''",
        'key' => "varchar(6) NOT NULL DEFAULT ''",
        'region' => "varchar(128) NOT NULL DEFAULT ''",
        'importance' => "int(11) NOT NULL DEFAULT '0'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => array('country_key', 'key')
    );
}