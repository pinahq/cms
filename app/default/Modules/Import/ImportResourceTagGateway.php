<?php

namespace Pina\Modules\Import;

use Pina\TableDataGateway;

class ImportResourceTagGateway extends TableDataGateway
{

    protected static $table = 'import_resource_tag';
    protected static $fields = array(
        'import_id' => "int(11) NOT NULL DEFAULT '0'",
        'resource_id' => "int(11) NOT NULL DEFAULT '0'",
        'tag_id' => "int(11) NOT NULL DEFAULT '0'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => ['import_id', 'resource_id', 'tag_id'],
    );

}
