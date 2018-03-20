<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('resources');

$resourceType = Request::input('resource_type');
$parentId = Request::input('parent_id');
$length = Request::input('length');

$gw = ResourceTreeGateway::instance();

if (!empty($resourceType)) {
    $resourceTypeId = ResourceTypeGateway::instance()->whereBy('type', $resourceType)->id();
    $gw->whereBy('resource_type_id', $resourceTypeId);
}
    $gw->whereBy('resource_enabled', 'Y')
    ->whereBy('resource_parent_id', !empty($parentId)?$parentId:0)
    ->whereBy('length', range(1, !empty($length)?$length:2))
    ->innerJoin(
        ResourceGateway::instance()->on('id', 'resource_id')->select('id')->select('parent_id')->select('title')->withUrl()
    );

$gw->orderBy('resource_tree.resource_order', 'asc');

$rs = $gw->get();

return ['resources' => $rs];
