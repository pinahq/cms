<?php

namespace Pina\Modules\CMS;

use Pina\Modules\Auth\Auth;
use Pina\Request;
use Pina\Response;
use Pina\Hash;
use Pina\App;

Request::match('cp/:cp/users/');

if (!Request::input('enabled')) {
    Request::set('enabled', 'N');
}

$data = Request::all();
if (isset($data['status']) && !in_array($data['status'], ['new', 'active', 'suspended', 'disabled'])) {
    return Response::badRequest(__('Укажите правильный статус'), 'status');
}

if (isset($data['group']) && !in_array($data['group'], ['unregistered', 'registered', 'root', 'manager'])) {
    return Response::badRequest(__('Укажите правильную группу'), 'status');
}

$me = Auth::user();
if ($data['group'] === 'root' && $me['group'] !== 'root') {
    return Response::badRequest(__('У вас нет прав на добавление пользователя в группу root'), 'group');
}

if (!empty($data['email'])) {
    $exists = UserGateway::instance()
        ->whereBy('email', $data['email'])
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

$id = UserGateway::instance()->insertGetId($data);

return Response::ok()->contentLocation(App::link('cp/:cp/users/:id', ['id' => $id]));
