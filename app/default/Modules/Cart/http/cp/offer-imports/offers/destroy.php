<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;
use Pina\Modules\CMS\ImportPreviewGateway;

Request::match('cp/:cp/offer-imports/:import_id/offers/:row');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);
if (empty($import) || !is_array($import) || $import['status'] != 'confirm') {
    return Response::notFound();
}

$row = Request::input('row');

ImportPreviewGateway::instance()->whereBy('import_id', $importId)->whereBy('row', $row)->delete();

return Response::ok();