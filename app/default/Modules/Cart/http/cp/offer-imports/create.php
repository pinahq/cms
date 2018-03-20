<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\Import\Reader;
use Pina\Modules\Import\ImportGateway;


$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

return [
    'formats' => Reader::getAvailableFormats(),
    'import' => $import,
];
