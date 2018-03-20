<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Paging;

Request::match('cp/:cp/payments');

$gw = PaymentGateway::instance()
	->innerJoin(
		PaymentMethodGateway::instance()
			->select('title')
			->on('id', 'payment_method_id')
	)
	->innerJoin(
		OrderGateway::instance()
			->on('id', 'order_id')
			->selectAs('id', 'order_id')
			->selectAs('number', 'order_number')
	)
	->orderBy('id', 'DESC')
	->select('id')
	->select('created')
	->select('total')
	->select('status');

if (Request::input('order_id')) {
	$gw->whereBy('order_id', Request::input('order_id'));
}

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 25);
$gw->paging($paging);

return [
    'payments' => $gw->get(),
    'paging' => $paging->fetch(),
];