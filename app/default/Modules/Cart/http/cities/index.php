<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cities');

$countryKey = Request::input('country_key');
$regionKey = Request::input('region_key');

$cs = CityGateway::instance()->whereBy('country_key', $countryKey)->whereBy('region_key', $regionKey)->get();

return ['cities' => $cs];