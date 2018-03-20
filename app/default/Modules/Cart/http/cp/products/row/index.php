<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/resources/:resource_id/row');

$r = Request::input('resource');

$price = ResourcePriceGateway::instance()->whereBy('resource_id', $r['id'])->first();
if (is_array($price)) {
    $r = array_merge($r, $price);
}

return $r;