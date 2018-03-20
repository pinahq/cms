<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:resource_id/copy');

$resourceId = Request::input('resource_id');

$newResourceId = Resource::handleCopy($resourceId);
if (empty($newResourceId)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('resources/:resource_id', ['resource_id' => $newResourceId]));