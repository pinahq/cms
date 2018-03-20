<?php
namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Import\ImportGateway;
use Pina\Modules\Import\Schema;

Request::match('cp/:cp/offer-imports/:import_id/schema');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

if ($import['status'] != 'confirm') {
    return Response::notFound();
}


$schema = json_decode($import['schema'], true);
$names = [];
$len = strlen('tag');
$offerTagLen=strlen('offer_tag');
foreach ($schema as $k => $v) {
    $name = '';
    if (strncmp($v, 'tag', $len) === 0) {
        $name = trim(substr($v, $len + 1));
        $schema[$k] = 'tag';
    }
    if (strncmp($v, 'offer_tag', $offerTagLen) === 0) {
        $name = trim(substr($v, $offerTagLen + 1));
        $schema[$k] = 'offer_tag';
    }
    $names[] = $name;
}

$header = json_decode($import['header'], true);

$list = array('' => 'Пропустить');
$list = array_merge($list, Schema::schemaFields());

return [
    'import' => $import,
    'schema' => $schema,
    'names' => $names,
    'header' => $header,
    'list' => $list,
];