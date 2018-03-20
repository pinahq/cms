<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('users/:user_id/orders');

$userId = Request::input('user_id');

$os = OrderGateway::instance()
    ->select('*')
    ->whereBy('user_id', $userId)
    ->withStatus()
    ->withCountryAndRegion()
    ->orderBy('id', 'desc')
    ->get();

return ['orders' => $os];