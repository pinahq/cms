<?php

namespace Pina\Modules\Users;

use Pina\Hash;
use Pina\Request;
use Pina\Response;
use Pina\Event;

$userId = Request::input('id');

Request::filter('strip_tags trim', 'firstname lastname');

if (!Request::input('subscribed')) {
    Request::set('subscribed', 'N');
}

$data = Request::all();
if (Request::input('new_password')) {
    if (Request::input('new_password') != Request::input('new_password2')) {
        return Response::badRequest(__('Пароль не совпадает'), 'new_password2');
    }
}

if (empty($data['lastname'])) {
    return Response::badRequest(__('Введите фамилию'), 'lastname');
}

if (empty($data['firstname'])) {
    return Response::badRequest(__('Введите имя'), 'firstname');
}

if (!in_array($data['subscribed'], ['N', 'Y'])) {
    return Response::badRequest(__('Укажите статус'), 'subscribed');
}

$user = UserGateway::instance()
	->whereBy('id', $userId)
	->first();
if (empty($user)) {
	return Response::notFound();
}

unset($data['password']);
if (!empty($data['new_password'])) {
    $data['password'] = Hash::make($data['new_password']);
}

unset($data["status"]);
unset($data['access_group']);

UserGateway::instance()->whereId($userId)->update($data);

if (isset($data['subscribed']) && $data['subscribed'] == 'Y') {
	Event::trigger('user.subscribed', $user['email']);
} else {
	Event::trigger('user.unsubscribed', $user['email']);
}

return Response::ok();