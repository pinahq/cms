<?php

namespace Pina;

use Pina\Response;
use Pina\Modules\Users\UserGateway;

$u = UserGateway::instance()->find(Request::input('id'));
if (empty($u)) {
    return Response::notFound();
}
return ["user" => $u];