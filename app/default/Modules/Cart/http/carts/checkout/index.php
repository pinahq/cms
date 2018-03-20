<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\Users\Auth;
use Pina\Modules\Users\UserGateway;

Request::match('carts/:cart_id/checkout');

$prefilled = null;

if (!empty(Auth::userId())) {
    $prefilled = OrderGateway::instance()->whereBy('user_id', Auth::userId())->orderBy('id', 'DESC')->first();
    
    if (empty($prefilled)) {
        $prefilled = UserGateway::instance()
            ->whereId(Auth::userId())
            ->select('firstname')
            ->select('lastname')
            ->select('middlename')
            ->select('phone')
            ->select('email')
            ->first();
    }
}

return [
    'user' => Auth::user(),
    'prefilled' => $prefilled,
    'shipping_enabled' => ShippingMethodGateway::instance()->whereBy('enabled', 'Y')->exists(),
    'payment_enabled' => PaymentMethodGateway::instance()->whereBy('enabled', 'Y')->exists(),
];
