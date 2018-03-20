<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/resource-types/:id');

$id = Request::input('id');

if (ResourceTypeGateway::instance()->whereNotBy('id', $id)->whereBy('title', Request::input('title'))->exists()) {
    return Response::badRequest('This title is already exists', 'title');
}

ResourceTypeGateway::instance()->whereId($id)->update(Request::all('title', 'pattern', 'cp_pattern'));

return Response::ok();
