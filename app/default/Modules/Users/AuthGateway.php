<?php

namespace Pina\Modules\Users;

use Pina\TableDataGateway;

class AuthGateway extends TableDataGateway
{
    protected static $table = 'auth';
    protected static $fields = array(
        'id'         => "VARCHAR(32) NOT NULL DEFAULT ''",
        'user_id'         => "INT(10) NOT NULL DEFAULT 0",
        'user_agent' => "VARCHAR(255) NOT NULL DEFAULT ''",
        'ip'         => "INT(10) UNSIGNED NOT NULL DEFAULT 0",
        'created'    => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
        'expired'    => "TIMESTAMP NULL DEFAULT NULL"
    );

    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'KEY expired' => 'expired',
    );

    public function getTriggers() 
    {
        return [
            [
                $this->getTable(),
                'after insert', 
                "INSERT INTO auth_history SET user_id = NEW.user_id, action='login'"
            ],
            [
                $this->getTable(),
                'after delete', 
                "INSERT INTO auth_history SET user_id = OLD.user_id, action='logout'"
            ]
        ];
    }
    
    public function add($data = array())
    {
        if (isset($data['ip'])) {
            $ip = $data['ip'];
            unset($data['ip']);
        }
        
        $q = "INSERT INTO `". $this->getTable() . "` SET ".
            $this->makeSetCondition($data, array_keys(static::$fields));
        
        if (isset($ip)) {
            $ip = ip2long($ip) === false ? 0 : $ip;
            $q .= ", `ip` = INET_ATON('$ip')";
        }
        
        return $this->db->query($q);
    }
    
    public function whereExpired($date)
    {
        $date = $this->db->escape($date);
        return $this->where("`expired` < STR_TO_DATE('". $date ."', '%Y-%m-%d %H:%i:%s')");
    }

}
