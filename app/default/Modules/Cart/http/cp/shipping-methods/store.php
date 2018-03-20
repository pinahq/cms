<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/shipping-methods');

$data = Request::all();

ShippingMethodGateway::instance()->insert($data);

return Response::found(App::link('cp/:cp/shipping-methods'));