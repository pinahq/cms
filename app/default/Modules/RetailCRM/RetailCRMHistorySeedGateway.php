<?php

namespace Pina\Modules\RetailCRM;

use Pina\TableDataGateway;

class RetailCRMHistorySeedGateway extends TableDataGateway
{

    protected static $table = 'retail_crm_history_seed';
    protected static $fields = array(
        'site' => "varchar(32) NOT NULL DEFAULT ''",
        'seed' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'site'
    );

}
