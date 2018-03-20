<?php

namespace Pina\Modules\Import;

use Pina\TableDataGateway;

class ImportErrorGateway extends TableDataGateway
{
    protected static $table = 'import_error';
    protected static $fields = array(
        'import_id' => "int(11) NOT NULL DEFAULT '0'",
        'row' => "int(11) NOT NULL DEFAULT '0'",
        'error' => "TEXT NOT NULL"//json
    );
    protected static $indexes = array(
        'PRIMARY KEY' => array('import_id', 'row'),
    );

}