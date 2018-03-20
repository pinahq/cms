<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Response;

if (Auth::check()) {
    return Response::ok()->json(['result' =>'user already logged in']);
} else {
    Request::filterAll('trim');
    
    $data = Request::only('email', 'password');
    
    if (empty($data['email'])) {
        return Response::badRequest(__('Введите E-mail'), 'email');
    }
    
    if (empty($data['password'])) {
        return Response::badRequest(__('Введите пароль'), 'password');
    }
    
    if (strlen($data['email']) > 64) {
        return Response::badRequest(__('E-mail должен быть не более 64 символов'), 'email');
    }
    
    if (strlen($data['password']) > 32) {
        return Response::badRequest(__('Пароль должен быть не более 32 символов'), 'password');
    }
    
    if (!Auth::attempt($data)) {
        return Response::badRequest(__('Неверный E-mail или пароль'), 'password');
    }
}

return Response::ok();