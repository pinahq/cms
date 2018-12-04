<?php

namespace Pina\Modules\CMS;

use Pina\Request;

$userId = Request::input("id");

$u = UserGateway::instance()->find($userId);
return ["user" => $u];