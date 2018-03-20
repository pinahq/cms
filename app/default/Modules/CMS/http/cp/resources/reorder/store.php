<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:parent_id/reorder');

$resourceIds = Request::input('resource_id');

$orders = ResourceGateway::instance()
    ->whereId($resourceIds)
    ->orderBy('order', 'asc')
    ->column('order');

$max = max($orders);

$last = null;
$diff = 0;
foreach ($orders as $k => $v) {
    $orders[$k] = intval($v + $diff);
    if ($last !== null && $orders[$k] == $last) {
        $diff ++;
        $orders[$k]++;
    }
    $last = $orders[$k];
}

if ($diff > 0) {
    ResourceGateway::instance()
        ->whereBetween('order', $max, 2147483647 - $diff - 1)
        ->increment('order', $diff + 1);
}
$i = 0;
foreach ($resourceIds as $resourceId) {
    $order = $orders[$i++];
    ResourceGateway::instance()
        ->whereId($resourceId)
        ->update(['order' => intval($order)]);
}

return Response::ok()->json([]);
