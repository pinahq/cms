<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;
use Pina\Modules\Auth\AuthGateway;

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
    
    public function getTriggers() 
    {
        $authTable = AuthGateway::instance()->getTable();
        return [
            [
                $authTable,
                'after insert', 
                "INSERT INTO auth_history SET user_id = NEW.user_id, action='login'"
            ],
            [
                $authTable,
                'after delete', 
                "INSERT INTO auth_history SET user_id = OLD.user_id, action='logout'"
            ]
        ];
    }
}
