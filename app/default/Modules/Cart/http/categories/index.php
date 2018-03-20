<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\Config;

Request::match('categories');

$parentId = Request::input('parent_id');
$length = Request::input('length');

$selectedTagIds = Request::input('tag_id');

$resourceTypeId = Request::input('resource_type_id');
if (empty($resourceTypeId)) {
    $resourceTypeId = \Pina\Modules\CMS\ResourceTypeGateway::instance()->whereBy('type', 'categories')->id();
}

$gw = ResourceTreeGatewayExtension::instance()->selectAs('resource_id', 'id')
    ->whereBy('resource_type_id', $resourceTypeId)
    ->whereBy('resource_enabled', 'Y')
    ->whereBy('resource_parent_id', !empty($parentId)?$parentId:0)
    ->whereBy('length', range(1, !empty($length)?$length:2));

if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
    $gw->whereInStock();
}
    
$rs = $gw->innerJoin(
        ResourceGatewayExtension::instance()->on('id', 'resource_id')->select('parent_id')->select('title')->withUrl()
    )
    ->orderBy('resource_tree.resource_order', 'asc')
    ->get();

return ['resources' => $rs];
