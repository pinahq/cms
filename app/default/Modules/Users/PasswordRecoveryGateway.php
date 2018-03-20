<?php

namespace Pina\Modules\Users;

use Pina\TableDataGateway;

class PasswordRecoveryGateway extends TableDataGateway
{

    protected static $table = "password_recovery";
    protected static $fields = array(
        'id' => "varchar(32) NOT NULL DEFAULT ''",
        'user_id' => "varchar(32) NOT NULL DEFAULT ''",
        'created' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id',
        'UNIQUE KEY user_id' => 'user_id',
    );

    public function generate()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $length = mt_rand(8, 32);

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }

        return $code;
    }

    public function insertGetId($data = array(), $fields = false)
    {
        $data[$this->primaryKey()] = $token = $this->getUniqueToken();
        
        $this->insert($data, $fields);

        return $token;
    }

    public function putGetId($data = array(), $fields = false)
    {
        $data[$this->primaryKey()] = $token = $this->getUniqueToken();
        
        $this->put($data, $fields);

        return $token;
    }

    protected function getUniqueToken()
    {
        do {
            $token = $this->generate();
        } while ($this->whereId($token)->exists());

        return $token;
    }

}
