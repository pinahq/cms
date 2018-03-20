<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/coupons/:coupon');

$coupon = Request::input('coupon');

$c = CouponGateway::instance()->find($coupon);

if (empty($c)) {
    return Response::notFound();
}

return ['coupon' => $c];