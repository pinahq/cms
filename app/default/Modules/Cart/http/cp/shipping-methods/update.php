<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/shipping-methods/:id');

$data = Request::only('title', 'description', 'enabled');

$shippingMethodId = Request::input('id');

if (empty($data['enabled'])) {
    $data['enabled'] = 'N';
}

ShippingMethodGateway::instance()->whereId($shippingMethodId)->update($data);

return Response::found(App::link('cp/:cp/shipping-methods'));