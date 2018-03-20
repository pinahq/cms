<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/orders/:id');

$orderId = Request::input('id');

$o = OrderGateway::instance()
    ->select('*')
    ->withCountryAndRegion()
    ->find($orderId);

return ['order' => $o];