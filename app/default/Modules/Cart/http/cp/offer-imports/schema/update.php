<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Import\ImportGateway;
use Pina\Event;

Request::match('cp/:cp/offer-imports/:import_id/schema');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    #return Response::notFound();
}

$schema = Request::input('schema');
$names = Request::input('names');
foreach ($schema as $k => $v) {
    if (in_array($v, ['tag', 'offer_tag']) && !empty($names[$k])) {
        $schema[$k] .= ' '.$names[$k];
    }
}

ImportGateway::instance()
    ->whereId($importId)
    ->update([
        "schema" => json_encode($schema, JSON_UNESCAPED_UNICODE)
    ]);

Event::trigger('catalog.build-import-preview', $importId);

return Response::ok();