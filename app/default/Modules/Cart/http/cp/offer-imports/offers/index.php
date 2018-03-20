<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Paging;
use Pina\Arr;
use Pina\App;
use Pina\Modules\Import\ImportGateway;
use Pina\Modules\Import\ImportPreviewGateway;
use Pina\Modules\Import\ImportErrorGateway;
use Pina\Modules\Import\Schema;

Request::match('cp/:cp/offer-imports/:import_id/offers');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    #return Response::notFound();
}

$paging = new Paging(Request::input('page'), 10);

$rows = ImportPreviewGateway::instance()
        ->select('*')
        ->leftJoin(
                ImportErrorGateway::instance()->on('import_id')->on('row')->select('error')
        )
        ->filter(Request::all())
        ->whereBy('import_id', $importId)
        ->paging($paging)
        ->get();

if (!is_array($rows)) {
    return Response::notFound();
}

foreach ($rows as $k => $row) {
    $rows[$k]['preview_cells'] = json_decode($row['preview']);
    $rows[$k]['error_cells'] = json_decode($row['error']);
}


$schema = json_decode($import['schema']);

$keyFields = array();
$defaultKeyField = '';
if (is_array($schema)) {
    foreach ($schema as $item) {
        if (in_array($item, array('offer_id', 'offer_external_id'))) {
            $keyFields[$item] = $item;
            if (empty($defaultKeyField)) {
                $defaultKeyField = $item;
            }
        }

        if (strncmp($item, "tag ", 4) === 0) {
            $keyFields['tag_type'][] = trim(substr($item, 4));
        }
    }
}

return [
    'import' => $import,
    'rows' => $rows,
    'header' => json_decode($import['header']),
    'filter' => Request::input('filter'),
    'key_fields' => $keyFields,
    'default_key_field' => $defaultKeyField,
    'schema' => Schema::prepareUserSchemaToDisplay($schema),
    'paging' => $paging->fetch(),
    'page_url' => App::resource(),
];
