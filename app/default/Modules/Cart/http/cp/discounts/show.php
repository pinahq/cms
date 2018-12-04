<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('cp/:cp/discounts/:id');

$id = Request::input('id');

$d = DiscountGateway::instance()
    ->select('*')
    ->find($id);

return ['discount' => $d];