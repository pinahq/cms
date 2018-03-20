<?php

namespace Pina\Modules\reCAPTCHA;

use Pina\TableDataGateway;

class RecaptchaResponseGateway extends TableDataGateway
{

    protected static $table = 'recaptcha_response';
    protected static $fields = [
        'response' => "varchar(512) NOT NULL DEFAULT ''",
        'json' => "varchar(512) NOT NULL DEFAULT ''",
        'created' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ];
    protected static $indexes = [
        'PRIMARY KEY' => ['response'],
    ];
    
    public function whereExpired()
    {
        return $this->where('created < DATE_SUB(NOW(), INTERVAL 10 minute)');
    }

}