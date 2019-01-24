<?php

namespace Pina\Modules\CMS;

use Pina\Request;

$userId = Request::input("id");

return ["user" => UserGateway::instance()->find($userId)];