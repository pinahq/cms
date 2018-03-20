<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;
use Pina\Arr;

Request::match('cp/:cp/orders/:order_id/statuses');

$orderId = Request::input('order_id');
$orderStatusId = Request::input('order_status_id');

$ss = OrderStatusGateway::instance()->get();

$status = null;
foreach ($ss as $s) {
    if ($s['id'] == $orderStatusId) {
        $status = $s;
        break;
    }
}

$groups = Arr::group($ss, 'group');

return [
    'status' => $status,
    'statusGroups' => $groups,
];
