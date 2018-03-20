<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('shipping-methods');

$countryKey = Request::input('country_key');
$regionKey = Request::input('region_key');
$cityId = Request::input('city_id');

return ['shipping_methods' => Shipping::getMethods($countryKey, $regionKey, $cityId)];
