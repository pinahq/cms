<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Import\ImportGateway;

Request::match('cp/:cp/offer-imports/:import_id/external-keys');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);
if (empty($import) || !is_array($import)) {
    return Response::notFound();
}


return [
    'import' => $import,
    'header' => json_decode($import['header'], true),
    'keys' => json_decode($import['keys'], true),
    'external_keys' => json_decode($import['external_keys'], true),
];