<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class ImportResourceGateway extends TableDataGateway
{
    protected static $table = 'import_resource';
    protected static $fields = array(
        'import_id' => "int(11) NOT NULL DEFAULT '0'",
        'resource_id' => "int(11) NOT NULL DEFAULT '0'",
        'status' => "enum('','added','updated','skipped') NOT NULL default ''",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => ['import_id', 'resource_id'],
    );

}