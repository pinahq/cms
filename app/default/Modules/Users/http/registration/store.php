<?php

namespace Pina\Modules\Users;

use Pina\Hash;
use Pina\Request;
use Pina\Response;
use Pina\Validate;
use Pina\Mail;
use Pina\App;

Request::filter('strip_tags trim', 'firstname lastname');
    
$user = Request::all();
$errors = [];
if (empty($user['email'])) {
    $errors[] = [__('Введите E-mail'), 'email'];
}

if (!Request::input('new_password')) {
    $errors[] = [__('Введите пароль'), 'new_password'];
}

if (Request::input('new_password') != Request::input('new_password2')) {
    $errors[] = [__('Пароль не совпадает'), 'new_password2'];
}

if (!filter_var(Request::input('email'), FILTER_VALIDATE_EMAIL)) {
    $errors[] = [__('Введите корректный email'), 'email'];
}

if ($errors) {
    return Response::badRequest()->setErrors($errors);
}

if ($captcha = App::container()->get('captcha')) {
    if (!$captcha->verify()) {
        return Response::badRequest(__('Пройдите проверку капчи'), 'captcha');
    }
}

if (!empty($user['email'])) {
	$existUser = UserGateway::instance()
		->whereBy('email', $user['email'])
		->first();
	if (!empty($existUser) && $existUser['group'] != 'unregistered') {
		return Response::badRequest('user already exists', 'email');
	}
}

$user['email'] = $user['email'];
$user['password'] = Hash::make($user['new_password']);
$user['status'] = 'new';
$user['access_group'] = 'registered';

$user['utm_source'] = isset($user['utm_source']) ? $user['utm_source'] : '';
$user['utm_medium'] =  isset($user['utm_medium']) ? $user['utm_medium'] : '';
$user['utm_campaign'] =  isset($user['utm_campaign']) ? $user['utm_campaign'] : '';
$user['utm_term'] =  isset($user['utm_term']) ? $user['utm_term'] : '';
$user['utm_content'] =  isset($user['utm_content']) ? $user['utm_content'] : '';

if (!empty($existUser) && $existUser['access_group'] == 'unregistered') {
	$userId = $existUser['id'];
	UserGateway::instance()
		->whereBy('id', $userId)
		->update($user);
} else {
	$userId = UserGateway::instance()->insertGetId($user);
}

Auth::loginUsingId($userId);

\Pina\Event::trigger('user.registered', array('id' => $userId));

if (isset($user['subscribed']) && $user['subscribed'] == 'Y') {
	\Pina\Event::trigger('user.subscribed', $user['email']);
} else if (!empty($existUser)) {
	\Pina\Event::trigger('user.unsubscribed', $user['email']);
}

return Response::created(App::link('users/:id', ['id' => $userId, 'display' => 'edit']));
