<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resource-types/:id');

$id = Request::input('id');
$rt = ResourceTypeGateway::instance()->find($id);

if (empty($rt)) {
    return Response::notFound();
}

return $rt;
