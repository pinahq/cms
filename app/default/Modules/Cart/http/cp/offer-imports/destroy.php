<?php
namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;
use Pina\Modules\CMS\ImportPreviewGateway;
use Pina\Modules\CMS\ImportErrorGateway;

Request::match('cp/:cp/offer-imports/:id');

$importId = Request::input("id");

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    return Response::badRequest(__('Import is still active'));
}

ImportGateway::instance()->whereId($importId)->delete();
ImportPreviewGateway::instance()->whereBy('import_id', $importId)->delete();
ImportErrorGateway::instance()->whereBy('import_id', $importId)->delete();

return Response::ok();