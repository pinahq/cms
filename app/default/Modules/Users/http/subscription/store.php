<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Response;
use Pina\Mail;
use Pina\Event;

Request::filter('trim', 'email');

if (!filter_var(Request::input('email'), FILTER_VALIDATE_EMAIL)) {
    return Response::badRequest(__('Введите корректный email'), 'email');
}

UserGateway::instance()->subscribe(Request::input('email'));

Event::trigger('user.subscribed', Request::input('email'));

return Response::ok();