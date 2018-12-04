<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;
use Pina\Event;
use Pina\App;

Request::match('cp/:cp/offer-imports/:import_id/replaces');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    return Response::notFound();
}

$replaces = Request::input('replaces');
$data = array();
foreach ($replaces as $k => $v) {
    if (empty($v['search']) && empty($v['replace'])) {
        unset($replaces[$k]);
        continue;
    } 
    $data[] = array($v['field'], $v['search'], $v['replace']);
}

ImportGateway::instance()->whereId($importId)->setReplaces($data);

Event::trigger('catalog.build-import-preview', $importId);

return Response::ok();