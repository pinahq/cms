<?php

namespace Pina\Modules\YandexKassa;

use Pina\Request;
use Pina\Response;
use Pina\App;

use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\PaymentGateway;
use Pina\Modules\Cart\PaymentMethodGateway;
use Pina\Modules\CMS\Config;

Request::match('yandex-kassa/:payment_id');

$payment = PaymentGateway::instance()
    ->innerJoin(PaymentMethodGateway::instance()->on('id', 'payment_method_id'))
    ->innerJoin(OrderGateway::instance()->on('id', 'order_id'))
    ->whereId(Request::input('payment_id'))
    ->first();
if (empty($payment)) {
    return Response::notFound();
}

$shopSuccessUrl = App::link('carts/:cart_id/orders/:order_id', ['cart_id' => $payment['cart_id'], 'order_id' => $payment['order_id']]);

$config = Config::getNamespace(__NAMESPACE__);
return [
    'payment' => $payment,
    'shop_success_url' => $shopSuccessUrl,
    'shopId' => $config['shopId'],
    'scid' => $config['scid'],
    'paymentType' => $config['paymentType'],
];
