<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/coupons/:coupon/status');

$coupon = Request::input('coupon');

CouponGateway::instance()->whereId($coupon)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => CouponGateway::instance()->whereId($coupon)->value('enabled')]);
