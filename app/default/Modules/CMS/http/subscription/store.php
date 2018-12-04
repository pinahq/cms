<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Mail;
use Pina\Event;

$email = trim(Request::input('email'));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return Response::badRequest(__('Введите корректный email'), 'email');
}

UserGateway::instance()->subscribe($email);

Event::trigger('user.subscribed', $email);

return Response::ok();