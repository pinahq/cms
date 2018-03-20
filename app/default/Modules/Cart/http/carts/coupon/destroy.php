<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('carts/:cart_id/coupon');

$cartId = Request::input('cart_id');

CartCouponGateway::instance()->whereBy('cart_id', $cartId)->delete();

return Response::ok();