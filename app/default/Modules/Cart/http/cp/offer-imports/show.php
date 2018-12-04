<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ImportGateway;

Request::match("cp/:cp/offer-imports/:import_id");

$importId = Request::input('import_id');

$i = ImportGateway::instance()->find($importId);
if (empty($i)) {
    return Response::notFound();
}

return ['import' => $i];
