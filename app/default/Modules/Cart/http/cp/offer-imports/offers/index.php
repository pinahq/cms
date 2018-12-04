<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Paging;
use Pina\Arr;
use Pina\App;
use Pina\Modules\CMS\ImportGateway;
use Pina\Modules\CMS\ImportPreviewGateway;
use Pina\Modules\CMS\ImportErrorGateway;

Request::match('cp/:cp/offer-imports/:import_id/offers');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import) || $import['status'] != 'confirm') {
    return Response::notFound();
}

$paging = new Paging(Request::input('page'), 10);

$rows = ImportPreviewGateway::instance()
        ->whereBy('import_id', $importId)
        ->filter(Request::all())
        ->select('*')
        ->leftJoin(
                ImportErrorGateway::instance()->on('import_id')->on('row')->select('error')
        )
        ->paging($paging)
        ->get();

foreach ($rows as $k => $row) {
    $rows[$k]['preview_cells'] = json_decode($row['preview']);
    $rows[$k]['error_cells'] = json_decode($row['error']);
}

$schema = json_decode($import['schema']);
$importSchema = new ImportOfferSchema();

return [
    'import' => $import,
    'rows' => $rows,
    'header' => json_decode($import['header']),
    'filter' => Request::input('filter'),
    'schema' => $importSchema->format($schema),
    'paging' => $paging->fetch(),
];
