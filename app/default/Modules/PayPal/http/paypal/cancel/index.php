<?php

namespace Pina\Modules\PayPal;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\PaymentGateway;

Request::match('paypal/:id/cancel');

$paymentId = Request::input('id');
$token = Request::input('token');
$payerId = Request::input('PayerID');

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
        ->selectName()
        ->selectAddress()
        ->withCountryAndRegion()
    )
    ->innerJoin(
        PayPalTokenGateway::instance()->on('payment_id', 'id')->onBy('token', $token)
    )
    ->whereValid('paypal', 'processed')
    ->find($paymentId);

if (empty($p)) {
    return Response::forbidden();
}

PaymentGateway::instance()->whereId($paymentId)->update(['status' => 'canceled']);
return Response::found(App::link('carts/:cart_id/payments/:id', ['cart_id' => $p['cart_id'], 'id' => $paymentId]))->json([]);
