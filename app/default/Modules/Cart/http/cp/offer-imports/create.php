<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ImportReaderRegistry;
use Pina\Modules\CMS\ImportGateway;


$importId = Request::input('import_id');

$import = ImportGateway::instance()->find($importId);

return [
    'formats' => ImportReaderRegistry::getAvailableFormats(),
    'import' => $import,
];
