<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\Config;

Request::match('cp/:cp/regions');

$rs = RegionGateway::instance()->orderBy('country_key', 'asc')->orderBy('key', 'asc')->get();

if (Request::input('download')) {
    if (Request::input('download') != 'csv') {
        return Response::badRequest();
    }

    $schema = [];
    $schema[] = ['country_key', 'Country'];
    $schema[] = ['key', 'Key'];
    $schema[] = ['region', 'Region'];
    $schema[] = ['importance', 'Importance'];

    $config = Config::getNamespace('Pina\\Modules\\Cart');

    $encoding = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
    $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
    $enclosure = '"';
    
    $csv = new \Pina\CSV($delimiter, $enclosure, $encoding);
    $csv->setSchema($schema);
    $csv->download('regions.csv', $rs);
    exit;
}

return ['regions' => $rs];