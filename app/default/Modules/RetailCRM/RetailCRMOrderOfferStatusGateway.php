<?php

namespace Pina\Modules\RetailCRM;

use Pina\TableDataGateway;

class RetailCRMOrderOfferStatusGateway extends TableDataGateway
{

    protected static $table = 'retail_crm_order_offer_status';
    protected static $fields = array(
        'status' => "varchar(32) NOT NULL DEFAULT ''",
        'title' => "varchar(32) NOT NULL DEFAULT ''",
        'decreased' => "enum('Y','N') NOT NULL DEFAULT 'Y'",
        'order_offer_status_id' => 'int(11) UNSIGNED NULL',
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'status',
        'UNIQUE KEY order_status' => 'order_offer_status_id',
    );

}