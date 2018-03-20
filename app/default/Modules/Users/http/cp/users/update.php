<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Response;
use Pina\Hash;

Request::match('cp/:cp/users/:id');

Request::filter('intval', 'id');
Request::filter('strip_tags trim', 'firstname lastname');

$data = Request::all();
if (isset($data['status']) && !in_array($data['status'], ['new', 'active', 'suspended', 'disabled'])) {
    return Response::badRequest(__('Укажите правильный статус'), 'status');
}

if (isset($data['group']) && !in_array($data['group'], ['unregistered','registered','root','manager'])) {
    return Response::badRequest(__('Укажите правильную группу'), 'status');
}

$me = Auth::user();
if ($data['group'] === 'root' && $me['group'] !== 'root') {
    return Response::badRequest(__('У вас нет прав на добавление пользователя в группу root'), 'group');
}

if (!empty($data['email'])) {
    $exists = UserGateway::instance()
        ->whereBy('email', $data['email'])
        ->whereNotBy('id', Request::input('id'))
        ->exists();
    if ($exists) {
        return Response::badRequest(__('Такой пользователь уже существует'), 'email');
    }
}

if (isset($data['password']) && empty($data['password'])) {
    unset($data['password']);
} elseif (!empty($data['password'])) {
    $data['password'] = Hash::make($data['password']);
}

UserGateway::instance()->whereId(Request::input('id'))->update($data);

return Response::ok();