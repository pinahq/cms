<?php

namespace Pina\Modules\CMS;

use Pina\Mail;
use Pina\Request;
use Pina\Response;

$data = Request::only('email');
if (empty($data['email'])) {
    return Response::badRequest(__('Введите E-mail'), 'email');
}

$userId = UserGateway::instance()->whereBy("email", $data['email'])->id();
if (empty($userId)) {
    return Response::badRequest(__('Такого пользователя не существует'), 'email');
}

$token = PasswordRecoveryGateway::instance()->putGetId(["user_id" => $userId]);

Mail::send('password-recovery', ["token" => $token, "user_id" => $userId]);

return Response::ok();