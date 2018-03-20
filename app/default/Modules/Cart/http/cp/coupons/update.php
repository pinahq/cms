<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/coupons/:coupon');

if (!Request::input('enabled')) {
    Request::set('enabled', 'N');
}

$coupon = Request::input('coupon');
if (empty($coupon)) {
    return Response::badRequest('Введите номер купона', 'coupon');
}

$coupon = Request::input('coupon');

CouponGateway::instance()->whereId($coupon)->update(Request::only('absolute', 'percent', 'enabled'));

return Response::ok();