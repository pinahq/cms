<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceTextGateway extends TableDataGateway
{

    protected static $table = 'resource_text';
    protected static $fields = array(
        'resource_id' => "int(10) NOT NULL DEFAULT 0",
        'text' => "text NOT NULL",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'resource_id',
        'FULLTEXT txt' => 'text'
    );

}
