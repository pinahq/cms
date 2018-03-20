<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Mail;

Request::match("password-recovery/:token/email");

$user = UserGateway::instance()->find(Request::input('user_id'));

Mail::to($user['email'], $user['firstname'].' '.$user['lastname']);