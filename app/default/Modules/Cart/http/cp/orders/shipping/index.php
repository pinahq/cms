<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/orders/:order_id/shipping');
$orderId = Request::input('order_id');

$o = OrderGateway::instance()
    ->select('*')
    ->find($orderId);


$sms = ShippingMethodGateway::instance()
    ->select('id')
    ->select('title')
    ->leftJoin(
        ShippingFeeGateway::instance()
            ->on('shipping_method_id', 'id')
            ->onBy('country_key', $o['country_key'])
            ->onBy('region_key', $o['region_key'])
            ->onBy('city_id', $o['city_id'])
            ->select('fee')
    )
    ->get();

return [
    'order' => $o,
    'shipping_methods' => $sms,
];