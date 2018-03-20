<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/resources');

if (Request::input('resource')) {
    if (ResourceGateway::instance()->whereBy('resource', Request::input('resource'))->exists()) {
        return Response::badRequest(__('Resource exists'), 'resource');
    }
}

$resourceId = Resource::handleCreate();

return Response::ok()->contentLocation(App::link('resources/:resource_id', ['resource_id' => $resourceId]));
