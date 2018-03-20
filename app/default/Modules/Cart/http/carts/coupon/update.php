<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('carts/:cart_id/coupon');

$cartId = Request::input('cart_id');
$coupon = Request::input('coupon');

if (!CouponGateway::instance()->whereBy('coupon', $coupon)->whereBy('enabled', 'Y')->exists()) {
    return Response::badRequest('Купон не существует', 'coupon');
}

CartCouponGateway::instance()->put(['cart_id' => $cartId, 'coupon' => $coupon]);

return Response::ok();