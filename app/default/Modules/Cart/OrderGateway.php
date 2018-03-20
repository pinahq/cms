<?php

namespace Pina\Modules\Cart;

use Pina\TableDataGateway;
use Pina\Modules\Users\UserGateway;

use Pina\Modules\Regions\CountryGateway;
use Pina\Modules\Regions\RegionGateway;

/*
update order set order_status_id = 1 where order_status_id = 0;
UPDATE order o left join retail_crm_order rco on rco.order_id = o.id
SET o.number = CONCAT(rco.retail_crm_order_id, 'A')
 */

class OrderGateway extends TableDataGateway
{

    protected static $table = 'order';
    protected static $fields = array(
        'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
        'cart_id' => "VARCHAR(36) NOT NULL DEFAULT ''",
        
        'number' => "VARCHAR(16) NOT NULL DEFAULT ''",
        
        'type' => "varchar(1) NOT NULL DEFAULT ''",
        
        'order_status_group' => "enum('placed','approval','assembling','delivering','complete','cancelled') NOT NULL DEFAULT 'placed'",

        'order_status_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        
        'amount' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        
        'user_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'subtotal' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        
        //отличается от shipping_fee тем, что у заказа в итоге может быть
        //скидка или наценка на доставку и здесь будет другое по сути значение.
        'shipping_subtotal' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'coupon_discount' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'total' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        'payed' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
        
        'coupon' => "varchar(32) NOT NULL DEFAULT ''",
        
        'address_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        
        'firstname' => "varchar(128) NOT NULL DEFAULT ''",
        'lastname' => "varchar(128) NOT NULL DEFAULT ''",
        'middlename' => "varchar(128) NOT NULL DEFAULT ''",
        
        'street' => "varchar(255) NOT NULL DEFAULT ''",
        'city_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'city' => "varchar(128) NOT NULL DEFAULT ''",
        'region_key' => "varchar(6) NOT NULL DEFAULT ''",
        'country_key' => "varchar(2) NOT NULL DEFAULT ''",
        'zip' => "varchar(32) NOT NULL DEFAULT ''",
        'phone' => "varchar(32) NOT NULL DEFAULT ''",
        'email' => "varchar(64) NOT NULL DEFAULT ''",
        
        'delivery_date' => "DATE NULL DEFAULT NULL",
        'delivery_time_from' => "TIME NOT NULL DEFAULT '00:00:00'",
        'delivery_time_to' => "TIME NOT NULL DEFAULT '00:00:00'",
        
        'manager_comment' => "varchar(1000) NOT NULL DEFAULT ''",
        'customer_comment' => "varchar(1000) NOT NULL DEFAULT ''",
        
        'shipping_method_id' => "int(11) UNSIGNED NOT NULL DEFAULT '0'",
        'shipping_method_title' => "varchar(255) NOT NULL DEFAULT ''",

        'utm_source' => "varchar(255) NOT NULL DEFAULT ''",
        'utm_medium' => "varchar(255) NOT NULL DEFAULT ''",
        'utm_campaign' => "varchar(255) NOT NULL DEFAULT ''",
        'utm_term' => "varchar(255) NOT NULL DEFAULT ''",
        'utm_content' => "varchar(255) NOT NULL DEFAULT ''",

        'subscribed' => "enum('N','Y') NOT NULL DEFAULT 'N'",
        
        'created' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
        'updated' => 'timestamp NULL',
    );
    protected static $indexes = array(
        'PRIMARY KEY' => 'id'
    );
    protected static $sorts = array();

    public function getTriggers()
    {
        $orderTotalCondition = 'SET NEW.total = NEW.subtotal + NEW.shipping_subtotal - NEW.coupon_discount;';
        $updatedCondition = 'SET NEW.updated = CURRENT_TIMESTAMP;';
        return [
            [
                $this->getTable(),
                'before insert',
                $orderTotalCondition
                .'IF (NEW.order_status_id IS NULL OR NEW.order_status_id = 0) THEN '
                    ." BEGIN"
                    ." SET NEW.order_status_group = 'placed';"
                    ." SET NEW.order_status_id = (SELECT order_status.id FROM order_status WHERE status = 'placed' LIMIT 1);"
                    ." END;"
                . " ELSE"
                    ." SET NEW.order_status_group = (SELECT `group` FROM order_status WHERE order_status.id = NEW.order_status_id LIMIT 1);"
                ." END IF;"
                .$updatedCondition
            ],
            [
                $this->getTable(),
                'before update',
                $orderTotalCondition
                .'IF (NEW.order_status_id IS NOT NULL) THEN '
                    ." SET NEW.order_status_group = (SELECT `group` FROM order_status WHERE order_status.id = NEW.order_status_id LIMIT 1);"
                ." END IF;"
                .$updatedCondition
            ],
        ];
    }
    
    public function withCountryAndRegion()
    {
        return $this->leftJoin(
            CountryGateway::instance()->on('key', 'country_key')->select('country')
        )->leftJoin(
            RegionGateway::instance()->on('key', 'region_key')->on('country_key', 'country.key')->select('region')
        );
    }
    
    public function withStatus()
    {
        return $this->leftJoin(
            OrderStatusGateway::instance()->on('id', 'order_status_id')
                ->selectAs('status', 'order_status')
                ->selectAs('id', 'order_status_id')
                ->selectAs('title', 'order_status_title')
                ->selectAs('color', 'order_status_color')
        );
    }
    
    public function selectName()
    {
        return $this->select('firstname')->select('lastname')->select('middlename');
    }
    
    public function selectAddress()
    {
        return $this
            ->select('street')
            ->select('city')
            ->select('region_key')
            ->select('country_key')
            ->select('zip')
            ->select('phone')
            ->select('email');
    }

}
