<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Event;
use Pina\Modules\CMS\ImportGateway;

Request::match("cp/:cp/offer-imports/:import_id");

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

ImportGateway::instance()->whereId($importId)->update([
    "last_row" => 0,
    "status" => "read",
]);

Event::trigger('catalog.build-import-preview', $importId);

return Response::ok();
