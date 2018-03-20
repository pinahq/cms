<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:resource_id/status');

$resourceId = Request::input('resource_id');

ResourceGateway::instance()->whereId($resourceId)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => ResourceGateway::instance()->whereId($resourceId)->value('enabled')]);