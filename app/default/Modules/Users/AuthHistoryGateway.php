<?php

namespace Pina\Modules\Users;

use Pina\TableDataGateway;

class AuthHistoryGateway extends TableDataGateway
{
    protected static $table = "auth_history";
    protected static $fields = array(
		'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'user_id' => "int(10) NOT NULL DEFAULT '0'",
		'action' => "enum('login','logout') NOT NULL DEFAULT 'login'",
		'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );
}
