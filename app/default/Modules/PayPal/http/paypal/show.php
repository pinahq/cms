<?php

namespace Pina\Modules\PayPal;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\PaymentGateway;

use PayPal\Service\PayPalAPIInterfaceServiceService;

Request::match('paypal/:id');

$paymentId = Request::input('id');

$p = PaymentGateway::instance()
    ->select('id')
    ->select('status')
    ->select('total')
    ->select('order_id')
    ->innerJoin(
        OrderGateway::instance()
        ->on('id', 'order_id')
        ->select('cart_id')
        ->selectName()
        ->selectAddress()
        ->withCountryAndRegion()
    )
    ->whereValid('paypal', 'new')
    ->find($paymentId);

if (empty($p)) {
    return Response::forbidden();
}

$requestBuilder = new RequestBuilder($p);
if (!$requestBuilder->hasConfig()) {
    return Response::internalError();
}

$paypalService = new PayPalAPIInterfaceServiceService($requestBuilder->getAcctAndConfig());
try {
    /* wrap API method calls on the service object with a try catch */
    $setECResponse = $paypalService->SetExpressCheckout($requestBuilder->getInitRequestDetails());
} catch (Exception $ex) {
    echo 'exception!!';
    print_r($ex);
    \Pina\Log::error('paypal', $ex->getMessage());
    return;
}

if (isset($setECResponse)) {
    if ($setECResponse->Ack == 'Success') {
        $token = $setECResponse->Token;
        
        PayPalTokenGateway::instance()->insert(['payment_id' => $p['id'], 'token' => $token]);
        PaymentGateway::instance()->whereId($p['id'])->update(['status' => 'processed']);
        $payPalURL = $requestBuilder->getUrl($token);
        return Response::found($payPalURL)->json([]);
    }
    
    \Pina\Log::error('paypal', join('|', $setECResponse->Errors));
}

return Response::found(App::link('carts/:cart_id/payments/:id', ['cart_id' => $p['cart_id'], 'id' => $paymentId]))->json([]);