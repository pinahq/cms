<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/resource-types');

if (ResourceTypeGateway::instance()->whereBy('title', Request::input('title'))->exists()) {
    return Response::badRequest('This title is already exists', 'title');
}

if (!Request::has('type')) {
    return Response::badRequest('Please enter type', 'type');
}

ResourceTypeGateway::instance()->insertGetId(Request::all('title', 'type', 'tree'));

return Response::ok();
