<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('carts/:cart_id/orders');
$cartId = Request::input('cart_id');
$orderId = Request::input('order_id');

$os = OrderGateway::instance()
    ->whereBy('cart_id', $cartId)
    ->select('*')
    ->withStatus()
    ->withCountryAndRegion()
    ->get();

if (empty($os)) {
    return Response::notFound();
}


return [
    'orders' => $os,
];