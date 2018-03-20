<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Response;
use Pina\Hash;
use Pina\App;

if (!Request::input('new_password')) {
    return Response::badRequest(__('Введите пароль'), 'new_password');
}

if (Request::input('new_password') != Request::input('new_password2')) {
    return Response::badRequest(__('Пароль не совпадает'), 'new_password2');
}

$token = Request::input("id");

$pr = PasswordRecoveryGateway::instance()->find($token);
if (empty($pr)) {
    return Response::notFound();
}

UserGateway::instance()->whereId($pr["user_id"])->update(array(
    "password" => Hash::make(Request::input("new_password")),
));
PasswordRecoveryGateway::instance()->whereId($token)->delete();

$link = App::link("auth", array("from" => "recovery"));

return Response::ok()->contentLocation($link);
