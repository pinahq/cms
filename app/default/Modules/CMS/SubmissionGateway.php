<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

class SubmissionGateway extends TableDataGateway
{

    protected static $table = 'submission';
    protected static $fields = array(
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'type' => "varchar(16) NOT NULL DEFAULT ''",
        'subject' => "varchar(255) NOT NULL DEFAULT ''",
        'email' => "varchar(255) NOT NULL DEFAULT ''",
        'firstname' => "varchar(255) NOT NULL DEFAULT ''",
        'lastname' => "varchar(255) NOT NULL DEFAULT ''",
        'middlename' => "varchar(255) NOT NULL DEFAULT ''",
        'phone' => "varchar(64) NOT NULL DEFAULT ''",
        'data' => "TEXT NULL",
        'user_id' => "INT(10) NOT NULL DEFAULT 0",
        'resource_id' => "INT(10) NOT NULL DEFAULT 0",
        'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
    );

}
