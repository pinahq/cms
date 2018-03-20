<?php

namespace Pina;

use Pina\Modules\Users\UserGateway;

$userId = Request::input("id");

$u = UserGateway::instance()->find($userId);
return ["user" => $u];