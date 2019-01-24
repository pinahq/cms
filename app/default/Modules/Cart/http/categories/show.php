<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('categories/:resource_id');

$resourceId = Request::input('resource_id');

$r = ResourceGatewayExtension::instance()
    ->select('*')
    ->whereId($resourceId)
    ->whereEnabled()
    ->withResourceText()
    ->first();

if (empty($r)) {
    return Response::notFound();
}

return ['resource' => $r];

