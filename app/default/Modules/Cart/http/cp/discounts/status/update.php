<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/discounts/:id/status');

$id = Request::input('id');

DiscountGateway::instance()->whereId($id)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => DiscountGateway::instance()->whereId($id)->value('enabled')]);
