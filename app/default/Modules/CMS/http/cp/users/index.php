<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Paging;
use Pina\App;

$gw = UserGateway::instance();

if (Request::input('enabled')) {
	$gw->whereBy('enabled', Request::input('enabled'));
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
