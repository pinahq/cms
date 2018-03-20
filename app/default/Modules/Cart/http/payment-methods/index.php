<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('payment-methods');

$ms = PaymentMethodGateway::instance()->enabled()->orderBy('order', 'asc')->get();
return ['payment_methods' => $ms];
