<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/users/:user_id');

Request::filter('intval', 'user_id');

$userId = Request::input('user_id');
if (empty($userId)) {
    return Response::badRequest();
}

UserGateway::instance()->whereId($userId)->delete();

return Response::ok();