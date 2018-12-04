<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\Config;

Request::match('cp/:cp/countries');

$cs = CountryGateway::instance()->get();

if (Request::input('download')) {
    if (Request::input('download') != 'csv') {
        return Response::badRequest();
    }

    $schema = [];
    $schema[] = ['key', 'Key'];
    $schema[] = ['country', 'Title'];
    $schema[] = ['importance', 'Importance'];

    $config = Config::getNamespace('Pina\\Modules\\Cart');

    $encoding = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
    $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
    $enclosure = '"';
    
    $csv = new \Pina\CSV($delimiter, $enclosure, $encoding);
    $csv->setSchema($schema);
    $csv->download('counties.csv', $cs);
    exit;
}

return ['countries' => $cs];