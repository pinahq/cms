<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\Modules\Users\Auth;

Request::match('carts/:cart_id/products');

$cartId = Request::input('cart_id');

$amounts = array_filter(Request::input('amount'));

if (empty($amounts)) {
    return Response::badRequest('Выберите количество');
}

if (is_array($amounts)) {
    foreach ($amounts as $offerId => $amount) {
        CartOfferGateway::instance()->addOfferAmount($cartId, $offerId, $amount, Auth::userId());
    }
}

return Response::ok()->contentLocation(App::link('carts/:cart_id', ['cart_id' => $cartId]));