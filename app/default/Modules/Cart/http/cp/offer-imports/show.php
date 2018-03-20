<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\Import\ImportGateway;

Request::match("cp/:cp/offer-imports/:import_id");

$importId = Request::input('import_id');

$i = ImportGateway::instance()->find($importId);

return ['import' => $i];
