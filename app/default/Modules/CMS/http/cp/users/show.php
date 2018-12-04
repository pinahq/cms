<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

$u = UserGateway::instance()->find(Request::input('id'));
if (empty($u)) {
    return Response::notFound();
}
return ["user" => $u];