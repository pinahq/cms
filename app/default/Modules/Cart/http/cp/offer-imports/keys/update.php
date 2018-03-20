<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Import\ImportGateway;

Request::match('cp/:cp/offer-imports/:import_id/keys');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    return Response::notFound();
}

$resourceKeys = array_keys(array_filter(Request::input('resource_keys')));

$offerKeys = array_keys(array_filter(Request::input('offer_keys')));

$importKeys = ['resource' => $resourceKeys, 'offer' => $offerKeys];

$oldImportKeys = json_decode($import['keys'], true);
if (empty($oldImportKeys)) {
    $oldImportKeys = ['resource' => [], 'offer' => []];
}

$needUpdate = false;
if (!array_diff($importKeys['resource'], $oldImportKeys['resource']) && !array_diff($oldImportKeys['resource'], $importKeys['resource'])) {
    $importKeys['resource'] = $oldImportKeys['resource'];
} else {
    $needUpdate = true;
}

if (!array_diff($importKeys['offer'], $oldImportKeys['offer']) && !array_diff($oldImportKeys['offer'], $importKeys['offer'])) {
    $importKeys['offer'] = $oldImportKeys['offer'];
} else {
    $needUpdate = true;
}

if ($needUpdate) {
    ImportGateway::instance()
        ->whereId($importId)
        ->update([
            "keys" => json_encode($importKeys, JSON_UNESCAPED_UNICODE)
        ]);
}

return Response::ok();