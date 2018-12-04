<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;
use Pina\Modules\CMS\ImportResourceGateway;

Request::match('cp/:cp/offer-imports/:import_id/results');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

$added = ImportResourceGateway::instance()
    ->whereBy('import_id', $import['id'])
    ->whereBy('status', 'added')
    ->count();

$updated = ImportResourceGateway::instance()
    ->whereBy('import_id', $import['id'])
    ->whereBy('status', 'updated')
    ->count();

$skipped = ImportResourceGateway::instance()
    ->whereBy('import_id', $import['id'])
    ->whereBy('status', 'skipped')
    ->count();

$offers = ImportResourceOfferGateway::instance()
    ->whereBy('import_id', $import['id'])
    ->count();

return [
    'import' => $import,
    'added' => $added,
    'updated' => $updated,
    'skipped' => $skipped,
    'offers' => $offers,
];
