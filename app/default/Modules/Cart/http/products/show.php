<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\DetailsTagTypeGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceUrlGateway;

Request::match('products/:resource_id');

$resourceId = Request::input('resource_id');

$gw = ResourceGatewayExtension::instance()
    ->whereId($resourceId)
    ->whereEnabled()
    ->select('*')
    ->withResourceType()
    ->withResourceText()
    ->withImage()
    //->withTags(DetailsTagTypeGateway::instance())
    ->withListTags()
    ->withPriceIfExists();

$needGroupBy = false;
$gw->withDiscount($needGroupBy);
if ($needGroupBy) {
    $gw->groupBy('resource.id');
}
    
$r = $gw->first();

if (empty($r) || empty($r['id'])) {
    return Response::notFound();
}

if (isset($r['discount_percent'])) {
    $r['actual_price'] = Discount::apply($r['price'], $r['actual_price'], $r['discount_percent']);
}

$tags = TagGateway::instance()
    ->select('id')
    ->select('tag')
    ->innerJoin(
        DetailsTagTypeGateway::instance()->on('tag_type_id')
    )
    ->innerJoin(
        ResourceTagGateway::instance()->on('tag_id', 'id')->onBy('resource_id', $resourceId)
    )
    ->leftJoin(
        ResourceGateway::instance()->on('id', 'resource_id')->onBy('enabled', 'Y')
        ->leftJoin(
            ResourceUrlGateway::instance()->on('resource_id', 'id')->select('url')
        )
    )
    ->orderBy('tag')
    ->get();

foreach ($tags as $k => $tag) {
    list($tags[$k]['type'], $tags[$k]['tag']) = explode(': ', $tag['tag'], 2);
}

return ['resource' => $r, 'tag_groups' => \Pina\Arr::group($tags, 'type')];
