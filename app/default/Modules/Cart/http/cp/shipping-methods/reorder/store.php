<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/shipping-methods/:parent_id/reorder');

$ids = Request::input('id');

ShippingMethodGateway::instance()->reorder($ids);

return Response::ok()->json([]);
