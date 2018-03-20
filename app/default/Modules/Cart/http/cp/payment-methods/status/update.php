<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/payment-methods/:id/status');

PaymentMethodGateway::instance()
	->whereId(Request::input('id'))
	->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => Request::input('enabled')]);
