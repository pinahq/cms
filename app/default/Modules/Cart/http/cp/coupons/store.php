<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/coupons');

$coupon = Request::input('coupon');
if (empty($coupon)) {
    return Response::badRequest('Введите номер купона', 'coupon');
}

if (CouponGateway::instance()->whereId($coupon)->exists()) {
    return Response::badRequest('Купон уже существует', 'coupon');
}

CouponGateway::instance()->insertIgnore(Request::all());

return Response::ok();