<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('pages/:resource_id');

$resourceId = Request::input('resource_id');

$r = ResourceGateway::instance()
    ->whereId($resourceId)
    ->select('*')
    ->whereEnabled()
    ->withResourceType()
    ->withResourceText()
    ->first();

if (empty($r)) {
    return Response::notFound();
}

return ['resource' => $r];

