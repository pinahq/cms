<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Config;

Request::match(':resource_type/:resource_id/same-title');

$resourceType = Request::input('resource_type');
$resourceId = Request::input('resource_id');
$limit = Request::input('limit');
if (empty($limit)) {
    $limit = 3;
}

$tagTypeIds = TagTypeGateway::instance()->whereBy('type', ['Бренд'])->column('id');

$gw = ResourceGatewayExtension::instance()
    ->select('id')
    ->select('title')
    ->whereNotBy('id', $resourceId)
    ->whereResourceType($resourceType)
    ->whereEnabled();

if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
    $gw->whereInStock();
}

$gw->innerJoin(
        ResourceGatewayExtension::instance()->alias('origin')->on('title')->onBy('id', $resourceId)
    )
    ->withImage()
    ->withUrl()
    ->withPrice()
    ->withListTags()
    ->limit($limit)
    ->groupBy('resource.id');
$rs = $gw->get();

return ['resources' => $rs];
