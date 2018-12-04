<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Arr;
use Pina\Modules\CMS\ImportGateway;

Request::match('cp/:cp/offer-imports/:import_id/settings');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    return Response::notFound();
}

$settings = json_decode($import['settings'], true);
$settings = Arr::merge($settings, Request::only('item_resource_type_id', 'parent_resource_type_id', 'resource_mode', 'offer_mode', 'resource_missing_status', 'offer_missing_status'));

ImportGateway::instance()->whereId($importId)->update(['settings' => json_encode($settings, JSON_UNESCAPED_UNICODE)]);

return Response::ok();