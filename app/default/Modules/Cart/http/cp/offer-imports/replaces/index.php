<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;

Request::match('cp/:cp/offer-imports/:import_id/replaces');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    return Response::notFound();
}


$header = json_decode($import['header'], true);

$shortenReplaces = json_decode($import['replaces'], true);
$replaces = array();
if (is_array($shortenReplaces))
foreach ($shortenReplaces as $line) {
    $replaces[] = array(
        'field' => $line[0],
        'search' => $line[1],
        'replace' => $line[2],
    );
}
return [
    'import' => $import,
    'header' => $header,
    'replaces' => $replaces,
];
