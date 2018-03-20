<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\TagGateway;

Request::match('collections/:resource_id');

$resourceId = Request::input('resource_id');

$r = ResourceGateway::instance()->whereId($resourceId)->select('*')
    ->whereEnabled()
    ->withResourceType()
    ->withResourceText()
    ->withImages()
    ->first();

if (empty($r)) {
    return Response::notFound();
}


$selectedTagIds = Request::input('tag_id');
if (!is_array($selectedTagIds)) {
    $selectedTagIds = [$selectedTagIds];
}

$resourceTagId = TagGateway::instance()->whereBy('resource_id', $resourceId)->id();

$products = new Products(Request::input('sort'), Request::input('page'));
return array_merge(['resource' => $r], $products->searchTagged(
    $resourceTagId,
    Request::input('tag_id'),
    Request::input('sale')
));