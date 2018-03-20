<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('carts/:cart_id/payments/:payment_id');

$paymentId = Request::input('id');

$p = PaymentGateway::instance()
    ->select('id')
    ->select('status')
    ->select('total')
    ->select('order_id')
    ->select('return_url')
    ->innerJoin(
        OrderGateway::instance()
        ->on('id', 'order_id')
        ->select('cart_id')
    )
    ->find($paymentId);

if (empty($p)) {
    return Response::notFound();
}

return $p;