<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('resources/:resource_id');

$resourceId = Request::input('resource_id');

$r = ResourceGateway::instance()
    ->whereId($resourceId)
    ->select('*')
    ->withResourceType()
    ->withUrl()
    ->first();

if (empty($r)) {
    return Response::notFound();
}

return Response::found('/'.$r['url']);