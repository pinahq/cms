<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;
use Pina\Modules\CMS\ResourceTypeGateway;

Request::match('cp/:cp/offer-imports/:import_id/settings');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

$import['settings'] = json_decode($import['settings'], true);

if (empty($import['settings']['item_resource_type_id'])) {
    $import['settings']['item_resource_type_id'] = ResourceTypeGateway::instance()->whereBy('type', 'products')->id();
}

if (empty($import['settings']['parent_resource_type_id'])) {
    $import['settings']['parent_resource_type_id'] = ResourceTypeGateway::instance()->whereBy('type', 'categories')->id();
}

return [
    'import' => $import,
];