<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Arr;
use Pina\Modules\CMS\Config;
use Pina\Modules\Regions\CountryGateway;
use Pina\Modules\Regions\RegionGateway;
use Pina\Modules\Regions\CityGateway;

Request::match('cp/:cp/shipping-methods/:shipping_method_id/fee');

$shippingMethodId = Request::input('shipping_method_id');

$sfs = CountryGateway::instance()->selectAs('key', 'country_key')->select('country')->leftJoin(
        RegionGateway::instance()->on('country_key', 'key')->selectAs('key', 'region_key')->select('region')->leftJoin(
            CityGateway::instance()->on('region_key', 'key')->on('country_key', 'country.key')->selectAs('id', 'city_id')->select('city')->leftJoin(
                ShippingFeeGateway::instance()
                    ->onBy('shipping_method_id', $shippingMethodId)
                    ->on('country_key', 'country.key')
                    ->on('region_key', 'region.key')
                    ->on('city_id', 'id')
                    ->select('fee')
            )
    ))->union(
        CountryGateway::instance()->selectAs('key', 'country_key')->select('country')->leftJoin(
            RegionGateway::instance()->on('country_key', 'key')->selectAs('key', 'region_key')->select('region')->leftJoin(
                ShippingFeeGateway::instance()
                    ->onBy('shipping_method_id', $shippingMethodId)
                    ->on('country_key', 'country.key')
                    ->on('region_key', 'region.key')
                    ->onBy('city_id', 0)
                    ->calculate('0 as city_id')
                    ->calculate("'' as city")
                    ->select('fee')
        ))
    )->union(
        CountryGateway::instance()->selectAs('key', 'country_key')->select('country')->leftJoin(
            ShippingFeeGateway::instance()
                ->onBy('shipping_method_id', $shippingMethodId)
                ->on('country_key', 'country.key')
                ->onBy('region_key', '')
                ->onBy('city_id', 0)
                ->calculate("'' as region_key")
                ->calculate("'' as region")
                ->calculate('0 as city_id')
                ->calculate("'' as city")
                ->select('fee')
        )->orderBy('country asc')->orderBy('region asc')->orderBy('city asc')//TODO: fix orderBy and union combination
    )->get();

if (Request::input('download')) {
    if (Request::input('download') != 'csv') {
        return Response::badRequest();
    }

    $schema = [];
    $schema[] = ['country', __('Country')];
    $schema[] = ['region', __('Region')];
    $schema[] = ['city', __('City')];
    $schema[] = ['fee', __('Price')];

    $config = Config::getNamespace('Pina\\Modules\\Catalog');

    $encoding = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
    $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
    $enclosure = '"';
    
    $csv = new \Pina\CSV($delimiter, $enclosure, $encoding);
    $csv->setSchema($schema);
    $csv->download('fee.csv', $sfs);
    exit;
}

return ['fees' => $sfs];
