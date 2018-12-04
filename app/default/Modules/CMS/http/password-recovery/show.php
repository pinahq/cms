<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

$token = Request::input("id");

if (!PasswordRecoveryGateway::instance()->whereId($token)->exists()) {
    return Response::notFound();
}

return ["token" => $token];