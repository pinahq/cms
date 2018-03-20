<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;
use Pina\Paging;

Request::match('cp/:cp/orders');

$gw = OrderGateway::instance()
    ->select('*')
    ->withStatus()
    ->withCountryAndRegion()
    ->orderBy('id', 'desc');

if (Request::exists('status')) {
    $gw->whereBy('order_status_group', Request::input('status'));
}

if (Request::input('date') === 'today') {
    $gw->whereLike('created', date('Y-m-d').'%');
}

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 25);
$gw->paging($paging);

return [
    'orders' => $gw->get(),
    'paging' => $paging->fetch(),
];
