<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ResourceUrlHistoryGateway extends TableDataGateway
{
    protected static $table = 'resource_url_history';
    protected static $fields = array(
        'resource_id' => "int(10) NOT NULL DEFAULT 0",
        'url' => "varchar(1000) NOT NULL DEFAULT ''",
        'created' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    );
    
    protected static $indexes = array(
        'KEY url' => 'url',
        'KEY resource_id' => 'resource_id',
    );
}