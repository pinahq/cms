<?php

namespace Pina\Modules\RetailCRM;

use Pina\TableDataGateway;

class RetailCRMOrderStatusGateway extends TableDataGateway
{

    protected static $table = 'retail_crm_order_status';
    protected static $fields = array(
        'status' => "varchar(32) NOT NULL DEFAULT ''",
        'title' => "varchar(32) NOT NULL DEFAULT ''",
        'order_status' => "varchar(32) NULL",
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'status',
        'UNIQUE KEY order_status' => 'order_status',
    );

}
