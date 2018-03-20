<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/orders/:order_id/shipping');
$orderId = Request::input('order_id');

$data = Request::only('shipping_method_id', 'shipping_subtotal');

if (!empty($data['shipping_method_id'])) {
    $data['shipping_method_title'] = ShippingMethodGateway::instance()->whereId($data['shipping_method_id'])->value('title');
}

$o = OrderGateway::instance()
    ->whereId($orderId)
    ->update($data);

return Response::ok();