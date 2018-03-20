<?php

namespace Pina\Modules\PayPal;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\PaymentGateway;

use PayPal\IPN\PPIPNMessage;

Request::match('paypal/:id/notify');

$paymentId = Request::input('id');

$p = PaymentGateway::instance()
    ->select('*')
    ->whereValid('paypal', ['processed', 'payed'])
    ->innerJoin(
        OrderGateway::instance()->on('id', 'order_id')->select('cart_id')
    )
    ->find($paymentId);

if (empty($p)) {
    return Response::forbidden()->json();
}

$requestBuilder = new RequestBuilder($p);
if (!$requestBuilder->hasConfig()) {
    return Response::internalError()->json();
}

$ipnMessage = new PPIPNMessage(null, $requestBuilder->getConfig()); 
if($ipnMessage->validate()) {
	return Response::ok()->json([]);
}

PaymentGateway::instance()->whereId($paymentId)->update(['status' => 'failed']);
return Response::ok()->json([]);
