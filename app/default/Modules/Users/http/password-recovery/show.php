<?php

namespace Pina;

use Pina\Response;
use Pina\Modules\Users\PasswordRecoveryGateway;

$token = Request::input("id");

if (!PasswordRecoveryGateway::instance()->whereId($token)->exists()) {
    return Response::notFound();
}

return ["token" => $token];