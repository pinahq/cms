<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Config;

Request::match(':resource_type/:resource_id/similars');

$resourceType = Request::input('resource_type');
$resourceId = Request::input('resource_id');
$limit = Request::input('limit');
if (empty($limit)) {
    $limit = 3;
}

$tagTypeIds = TagTypeGateway::instance()->whereBy('type', ['Цвет'])->column('id');

$gw = ResourceGatewayExtension::instance()
    ->select('id')
    ->select('title')
    ->whereNotBy('id', $resourceId)
    ->whereResourceType($resourceType)
    ->whereEnabled();

if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
    $gw->whereInStock();
}

$needGroupBy = false;

$gw->innerJoin(
        ResourceTagGateway::instance()->on('resource_id', 'id')
        ->innerJoin(
            TagGateway::instance()->on('id', 'tag_id')->onBy('tag_type_id', $tagTypeIds)
        )
        ->innerJoin(
            ResourceTagGateway::instance()->alias('tags')->on('tag_id')->onBy('resource_id', $resourceId)
        )
    )
    ->withImage()
    ->withUrl()
    ->withPrice()
    ->withDiscount($needGroupBy)
    ->withListTags()
    ->limit($limit)
    ->groupBy('resource.id');

$rs = $gw->get();

$rs = Discount::applyList($rs, 'discount_percent');

return ['resources' => $rs];
