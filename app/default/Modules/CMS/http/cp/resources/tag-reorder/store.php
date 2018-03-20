<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:parent_id/tag-reorder');

$parentId = Request::input('parent_id');
$tagId = TagGateway::instance()->whereBy('resource_id', $parentId)->id();

$resourceIds = Request::input('resource_id');

$orders = ResourceTagGateway::instance()
    ->whereBy('tag_id', $tagId)
    ->whereBy('resource_id', $resourceIds)
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
    ResourceTagGateway::instance()
        ->whereBy('tag_id', $tagId)
        ->whereBetween('order', $max, 2147483647 - $diff - 1)
        ->increment('order', $diff + 1);
    
}

$i = 0;
foreach ($resourceIds as $order => $resourceId) {
    $order = $orders[$i++];
    ResourceTagGateway::instance()
        ->whereBy('tag_id', $tagId)
        ->whereBy('resource_id', $resourceId)
        ->update(['order' => intval($order)]);
}

return Response::ok();
