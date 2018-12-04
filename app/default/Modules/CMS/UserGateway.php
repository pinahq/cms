<?php

namespace Pina\Modules\CMS;

use Pina\TableDataGateway;

/*
  INSERT INTO user (id, email, password, firstname, middlename, phone, status, `group`, subscribed, created, utm_source, utm_medium, utm_campaign, utm_term, utm_content)
  SELECT user_id, user_email, user_password, user_firstname, user_middlename, user_phone, user_status, user_access_group, user_subscribed, user_created, user_utm_source, user_utm_medium, user_utm_campaign, user_utm_term, user_utm_content FROM cody_user;
 */

class UserGateway extends TableDataGateway
{

    protected static $table = 'user';
    protected static $fields = [
        'id' => "int(10) NOT NULL AUTO_INCREMENT",
        'email' => "varchar(64) NOT NULL DEFAULT ''",
        'password' => "varchar(64) NOT NULL DEFAULT ''",
        'firstname' => "varchar(64) NOT NULL DEFAULT ''",
        'lastname' => "varchar(64) NOT NULL DEFAULT ''",
        'middlename' => "varchar(64) NOT NULL DEFAULT ''",
        'phone' => "varchar(64) NOT NULL DEFAULT ''",
        'enabled' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        //TODO: delete status field
        'status' => "enum('new','active','suspended','disabled') NOT NULL DEFAULT 'new'",
        'group' => "enum('unregistered','registered','root','manager') NOT NULL DEFAULT 'registered'",
        'subscribed' => "enum('N','Y') NOT NULL DEFAULT 'N'",
        'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
        'utm_source' => "varchar(32) NOT NULL DEFAULT ''",
        'utm_medium' => "varchar(32) NOT NULL DEFAULT ''",
        'utm_campaign' => "varchar(32) NOT NULL DEFAULT ''",
        'utm_term' => "varchar(32) NOT NULL DEFAULT ''",
        'utm_content' => "varchar(32) NOT NULL DEFAULT ''",
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY email' => 'email',
    ];

    public function subscribe($data)
    {
        if (!is_array($data)) {
            $data = ['email' => $data];
        }
        if (!isset($data['email'])) {
            return;
        }

        $data['email'] = $this->db->escape(trim($data['email']));
        if (empty($data['email'])) {
            return false;
        }

        $only = ['email', 'phone', 'firstname', 'lastname', 'middlename'];
        $user = [];
        foreach ($only as $key) {
            if (!isset($data[$key])) {
                continue;
            }
            $user[$key] = $data[$key];
        }
        $user['group'] = 'unregistered';
        $user['subscribed'] = 'Y';

        $q = "INSERT INTO `" . $this->getTable() . "` SET " . static::makeSetCondition($user)
            . " ON DUPLICATE KEY UPDATE `subscribed` = 'Y'";
        
        return $this->db->query($q);
    }

}
