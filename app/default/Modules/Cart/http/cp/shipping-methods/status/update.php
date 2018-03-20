<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/shipping-methods/:id/status');

$id = Request::input('id');

ShippingMethodGateway::instance()->whereId($id)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => ShippingMethodGateway::instance()->whereId($id)->value('enabled')]);
