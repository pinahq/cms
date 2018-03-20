<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('carts/:cart_id/coupon');

$cartId = Request::input('cart_id');

$coupon = CartCouponGateway::instance()->whereBy('cart_id', $cartId)->value('coupon');

return ['coupon' => $coupon];