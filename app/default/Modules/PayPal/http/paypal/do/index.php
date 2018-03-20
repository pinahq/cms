<?php

namespace Pina\Modules\PayPal;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\PaymentGateway;

use PayPal\Service\PayPalAPIInterfaceServiceService;

Request::match('paypal/:id/do');

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

$requestBuilder = new RequestBuilder($p);
if (!$requestBuilder->hasConfig()) {
    return Response::internalError();
}

$paypalService = new PayPalAPIInterfaceServiceService($requestBuilder->getAcctAndConfig());
$DoECResponse = $paypalService->DoExpressCheckoutPayment($requestBuilder->getDoRequestDetails($payerId, $token));

if ($DoECResponse->Ack == 'Success') {
    PaymentGateway::instance()->whereId($paymentId)->update(['status' => 'payed']);
    $returnUrl = !empty($p['return_url'])?$p['return_url']:App::link('carts/:cart_id/orders/:order_id', ['cart_id' => $p['cart_id'], 'order_id' => $p['order_id']]);
    return Response::found($returnUrl)->json([]);
}

\Pina\Log::error('paypal', join('|', $DoECResponse->Errors));
PaymentGateway::instance()->whereId($paymentId)->update(['status' => 'failed']);
return Response::found(App::link('carts/:cart_id/payments/:id', ['cart_id' => $p['cart_id'], 'id' => $paymentId]))->json([]);
