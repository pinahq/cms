<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\Config;

Request::match('cp/:cp/countries');

if (\Pina\Input::getContentType() == 'text/csv') {

    $config = Config::getNamespace('Pina\\Modules\\Cart');

    $encoding = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
    $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
    $enclosure = '"';

    $base64 = file_get_contents("php://input");
    $parts = explode(',', $base64);
    $csv = base64_decode($parts[1]);
    
    $import = new CountryImport($delimiter, $enclosure, $encoding);
    $import->importFromString($csv);
    
    return Response::ok()->json();
}

return Response::ok()->json();
