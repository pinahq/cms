<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Mail;

$u = UserGateway::instance()
	->whereBy('email', Request::input('email'))
	->whereBy('subscribed', 'Y')
	->first();

Mail::to($u['email']);

return ['subscription' => $u];