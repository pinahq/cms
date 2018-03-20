<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:id');

$resourceId = Request::input('id');

$url = ResourceUrlGateway::instance()->innerJoin(
    ResourceGateway::instance()->on('id', 'resource_id')->whereId($resourceId)
)->value('url');

if (empty($url)) {
    return Response::notFound();
}

if (!Request::input('enabled')) {
    Request::set('enabled', 'N');
}

if (Request::input('resource')) {
    if (ResourceGateway::instance()->whereBy('resource', Request::input('resource'))->whereNotBy('id', $resourceId)->exists()) {
        return Response::badRequest(__('Resource exists'), 'resource');
    }
}

Resource::handleUpdate($resourceId);

if (Request::input('enabled') == 'Y') {
    return Response::ok()->contentLocation('/' . $url);
}

return Response::ok();