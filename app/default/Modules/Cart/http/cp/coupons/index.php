<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('cp/:cp/coupons');

$cs = CouponGateway::instance()->get();

return ['coupons' => $cs];