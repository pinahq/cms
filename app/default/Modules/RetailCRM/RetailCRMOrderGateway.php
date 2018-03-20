<?php

namespace Pina\Modules\RetailCRM;

use Pina\TableDataGateway;

class RetailCRMOrderGateway extends TableDataGateway
{

    protected static $table = 'retail_crm_order';
    protected static $fields = array(
        'id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'order_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'created' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'order_id'
    );

}
