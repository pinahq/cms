<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/payment-methods/:id');

$m = PaymentMethodGateway::instance()->whereId(Request::input('id'))->first();
return ['m' => $m];
