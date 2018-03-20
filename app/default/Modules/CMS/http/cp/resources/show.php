<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/:resource_type/:resource_id');

$resourceId = Request::input('resource_id');

$r = ResourceGateway::instance()
    ->whereId($resourceId)
    ->select('*')
    ->withResourceType()
    ->withResourceText()
    ->withResourceMeta()
    ->withUrl()
    ->withChildCount()
    ->first();

if (empty($r)) {
    return Response::notFound();
}

$count = ResourceTagGateway::instance()
    ->innerJoin(
        TagGateway::instance()->on('id', 'tag_id')->onBy('resource_id', $resourceId)
    )
    ->count();

return [
    'resource' => $r,
    'count' => $count
];
