<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/shipping-methods/:id');

$sm = ShippingMethodGateway::instance()->find(Request::input('id'));
return ['shipping_method' => $sm];