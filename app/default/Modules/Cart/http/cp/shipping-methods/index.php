<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('cp/:cp/shipping-methods');

$sms = ShippingMethodGateway::instance()->orderBy('order', 'asc')->get();

return ['shipping_methods' => $sms];