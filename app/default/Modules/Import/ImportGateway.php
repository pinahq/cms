<?php

namespace Pina\Modules\Import;

use Pina\TableDataGateway;


class ImportGateway extends TableDataGateway
{
    protected static $table = 'import';
    protected static $fields = array(
        'id' => "int(11) NOT NULL AUTO_INCREMENT",
        
        'header_row' => "int(11) NOT NULL DEFAULT '0'",
        'start_row' => "int(11) NOT NULL DEFAULT '0'",
        'last_row' => "int(11) NOT NULL DEFAULT '0'",
        
        'format' => "varchar(32) NOT NULL DEFAULT ''",
        'file_name' => "varchar(255) NOT NULL DEFAULT ''",
        'file_header' => "varchar(2500) NOT NULL DEFAULT ''",
        'path' => "varchar(255) NOT NULL DEFAULT ''",
        
        'header' => "varchar(2500) NOT NULL DEFAULT ''",
        'schema' => "text NULL",
        'field_titles' => "varchar(2500) NOT NULL DEFAULT ''",//deprecated
        'presented_fields' => "varchar(2500) NOT NULL DEFAULT ''",//deprecated
        'fields' => "varchar(2500) NOT NULL DEFAULT ''",
        'replaces' => "mediumtext NULL",
        'keys' => "varchar(512) NOT NULL DEFAULT ''",
        'external_keys' => "varchar(255) NOT NULL DEFAULT ''",
        
        'missing_status' => "enum('','out','hidden','deleted') NOT NULL DEFAULT ''",
        'status' => "enum('read','confirm','import','done') NOT NULL DEFAULT 'read'",
        'mode' => "enum('','create','update') NOT NULL DEFAULT ''",
        'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );
}
