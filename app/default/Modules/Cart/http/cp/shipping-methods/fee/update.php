<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\Config;

Request::match('cp/:cp/shipping-methods/:shipping_method_id/fee');

$shippingMethodId = Request::input('shipping_method_id');

if (\Pina\Input::getContentType() == 'text/csv') {

    $config = Config::getNamespace('Pina\\Modules\\Cart');

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
    foreach ($header as $key => $value) {
        $header[$key] = ($encoding != 'utf8') ? iconv($encoding, 'utf8', $value) : $value;
    }
    
    $countries = CountryGateway::instance()->get();
    $regions = RegionGateway::instance()->get();
    $cities = CityGateway::instance()->get();
    
    $data = [];
    while ($line = fgetcsv($handle, 0, $delimiter, $enclosure)) {
        $item = [];
        foreach ($schema as $spec) {
            list($key, $title) = $spec;
            $value = '';
            foreach ($header as $index => $v) {
                if ($v == $title) {
                    $value = $line[$index] ? $line[$index] : '';
                    break;
                }
            }
            $item[$key] = ($encoding != 'utf8') ? iconv($encoding, 'utf8', $value) : $value;
        }
        
        $item['country_key'] = '';
        foreach ($countries as $c) {
            if ($c['country'] == $item['country']) {
                $item['country_key'] = $c['key'];
                break;
            }
        }
        if (!empty($item['country']) && empty($item['country_key'])) {
            continue;
        }
        
        $item['region_key'] = '';
        foreach ($regions as $r) {
            if ($r['region'] == $item['region'] && $r['country_key'] == $item['country_key']) {
                $item['region_key'] = $r['key'];
                break;
            }
        }
        if (!empty($item['region']) && empty($item['region_key'])) {
            continue;
        }
        
        $item['city_id'] = 0;
        foreach ($cities as $c) {
            if ($c['city'] == $item['city'] && $c['region_key'] == $item['region_key'] && $c['country_key'] == $item['country_key']) {
                $item['city_id'] = $c['id'];
                break;
            }
        }
        if (!empty($item['city']) && empty($item['city_id'])) {
            continue;
        }
        
        if ($item['fee'] === '') {
            ShippingFeeGateway::instance()
                ->whereBy('shipping_method_id', $shippingMethodId)
                ->whereBy('country_key', $item['country_key'])
                ->whereBy('region_key', $item['region_key'])
                ->whereBy('city_id', $item['city_id'])
                ->delete();
            continue;
        }
        
        $data[] = $item;
        
    }
    ShippingFeeGateway::instance()->context('shipping_method_id', $shippingMethodId)->put($data);
    return Response::ok()->json();
}

$countries = Request::input('fee');

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
