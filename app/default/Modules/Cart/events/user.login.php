<?php

namespace Pina\Modules\Cart;

use Pina\Event;

$userId = Event::data();

$cartId = !empty($_COOKIE['cart_id'])?$_COOKIE['cart_id']:'';

if (empty($cartId) && $userId) {
    $cartId = CartOfferGateway::instance()->whereBy('user_id', $userId)->orderBy('created', 'desc')->value('cart_id');
    if (!empty($cartId)) {
        setcookie('cart_id', $cartId, 0, '/');
    }
}
