<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceMetaGateway extends TableDataGateway
{

    protected static $table = 'resource_meta';
    protected static $fields = array(
        'resource_id' => "int(10) NOT NULL DEFAULT 0",
        'title' => "varchar(255) NOT NULL DEFAULT ''",
        'description' => "varchar(255) NOT NULL DEFAULT ''",
        'keywords' => "varchar(255) NOT NULL DEFAULT ''",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'resource_id',
    );

}
