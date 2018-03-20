<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Import\ImportGateway;
use Pina\Modules\Import\Schema;

Request::match('cp/:cp/offer-imports/:import_id/keys');

$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

if (empty($import) || !is_array($import)) {
    return Response::notFound();
}

$schema = json_decode($import['schema'], true);

$keyInfo = Schema::schemaKeyInfo();
$keySchema = [];
foreach ($schema as $line) {
	$parts = explode(' ', $line);
	$keySchema[] = isset($keyInfo[$parts[0]])?$keyInfo[$parts[0]]:'';
}

return [
    'import' => $import,
    'schema' => Schema::prepareUserSchemaToDisplay($schema),
    'key_schema' => $keySchema,
    'header' => json_decode($import['header'], true),
    'keys' => json_decode($import['keys'], true)
];