<?php

namespace Pina\Modules\MailChimp;

use Pina\TableDataGateway;

class MailchimpSubscriptionGateway extends TableDataGateway
{
    protected static $table = 'mailchimp_subscription';
    protected static $fields = [
        'email' => "VARCHAR(64) NOT NULL DEFAULT ''",
        'status' => "ENUM('subscribed','unsubscribed','cleaned','pending','error') NOT NULL DEFAULT 'subscribed'",
        'result' => "TEXT NULL DEFAULT NULL",
        'updated' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    protected static $indexes = [
        'PRIMARY KEY' => 'email'
    ];
}
