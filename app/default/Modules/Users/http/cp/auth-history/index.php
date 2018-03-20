<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Paging;

$gw = AuthHistoryGateway::instance()
	->orderBy('id DESC')
    ->select('id')
	->select('action')
	->select('created')
    ->innerJoin(
        UserGateway::instance()->on('id', 'user_id')->select('email')
    );

$paging = new Paging(Request::input('page'), Request::input("paging")?Request::input("paging"):30);

$items = $gw->paging($paging)->get();

return [
    'history' => $items,
    'paging' => $paging->fetch(),
];