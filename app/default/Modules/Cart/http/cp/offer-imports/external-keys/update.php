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

if ($import['status'] != 'confirm') {
    return Response::notFound();
}

$resourceKeys = array_map('intval', explode(",", Request::input('resource_keys')));
$offerKeys = array_map('intval', explode(",", Request::input('offer_keys')));
$importKeys = ['resource' => $resourceKeys, 'offer' => $offerKeys];

$importExtenalKeys = [];
$externalKeys = Request::input('external_keys');
if (!empty($externalKeys) && is_array($externalKeys)) {
    $importExtenalKeys = array_keys(Request::input('external_keys'));
}

ImportGateway::instance()
    ->whereId($importId)
    ->update([
        "keys" => json_encode($importKeys, JSON_UNESCAPED_UNICODE),
        'external_keys' => json_encode($importExtenalKeys, JSON_UNESCAPED_UNICODE),
    ]);

return Response::ok();