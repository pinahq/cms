<?php

namespace Pina;

use Pina\Modules\Users\User;
use Pina\Modules\Users\UserGateway;

$gw = UserGateway::instance();

if (Request::input('status')) {
	$gw->whereBy('status', Request::input('status'));
}

if (Request::input('subscribed')) {
	$gw->whereBy('subscribed', Request::input('subscribed'));
}

if (Request::input('search')) {
	$search = Request::input('search');
	$gw->whereLike(['firstname', 'lastname', 'phone', 'email'], "%$search%");
}

$info = pathinfo(App::resource());
$isDownloading = !empty($info['extension']) && in_array($info['extension'], ['csv']);

if ($isDownloading) {
    User::download($gw);
    exit;
}

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 25);
$gw->paging($paging);

return [
    'users' => $gw->get(),
    'paging' => $paging->fetch(),
];
