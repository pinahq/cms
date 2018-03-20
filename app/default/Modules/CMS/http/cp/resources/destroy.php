<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:id');

$resourceId = Request::input('id');

if (ResourceGateway::instance()->whereBy('parent_id', $resourceId)->exists()) {
    return Response::badRequest(__("Can't delete the page with nested pages."));
}

ResourceGateway::instance()->whereId($resourceId)->delete();

return Response::ok();