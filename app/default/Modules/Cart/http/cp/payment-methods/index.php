<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/payment-methods');

$gw = PaymentMethodGateway::instance()->orderBy('order', 'asc');
return ['methods' => $gw->get()];
