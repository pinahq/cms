<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\Config;

Request::match('cp/:cp/shipping-methods/:shipping_method_id/fee');

$shippingMethodId = Request::input('shipping_method_id');
$countries = Request::input('fee');

if (\Pina\Input::getContentType() == 'text/csv') {

    $config = Config::getNamespace('Pina\\Modules\\Catalog');

    $encoding = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
    $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
    $enclosure = '"';

    $schema = [];
    $schema[] = ['country', __('Country')];
    $schema[] = ['region', __('Region')];
    $schema[] = ['city', __('City')];
    $schema[] = ['fee', __('Price')];

    $base64 = file_get_contents("php://input");
    $parts = explode(',', $base64);
    $csv = base64_decode($parts[1]);
    
    $handle = fopen("php://memory", "r+");
    fwrite($handle, $csv);
    rewind($handle);
    $header = fgetcsv($handle, 0, $delimiter, $enclosure);
    while ($line = fgetcsv($handle, 0, $delimiter, $enclosure)) {
        $data = [];
        foreach ($schema as $spec) {
            list($key, $title) = $spec;
            $value = '';
            foreach ($header as $index => $v) {
                if ($v == $title) {
                    $value = $line[$index] ? $line[$index] : '';
                    break;
                }
            }
            $data[$key] = ($encoding != 'utf8') ? iconv($encoding, 'utf8', $value) : $value;
        }
    }
    exit;
}

$data = [];
foreach ($countries as $countryKey => $regions) {
    foreach ($regions as $regionKey => $cities) {
        foreach ($cities as $cityId => $fee) {
            if ($fee['fee'] === '') {
                ShippingFeeGateway::instance()
                    ->whereBy('shipping_method_id', $shippingMethodId)
                    ->whereBy('country_key', !empty($countryKey) ? $countryKey : '')
                    ->whereBy('region_key', !empty($regionKey) ? $regionKey : '')
                    ->whereBy('city_id', !empty($cityId) ? $cityId : 0)
                    ->delete();
                continue;
            }

            $data[] = array_merge([
                'shipping_method_id' => $shippingMethodId,
                'country_key' => $countryKey ? $countryKey : '',
                'region_key' => $regionKey ? $regionKey : '',
                'city_id' => $cityId,
                ], $fee);
        }
    }
}

ShippingFeeGateway::instance()->put($data);

return Response::ok()->json();
