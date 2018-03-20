<?php

namespace Pina\Modules\Regions;

use Pina\Request;

Request::match('regions');

$countryKey = Request::input('country_key');

$rs = RegionGateway::instance()->whereBy('country_key', $countryKey)->orderBy('importance', 'DESC')->orderBy('region', 'ASC')->get();

return ['regions' => $rs];