<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:parent_id/reorder');

$resourceIds = Request::input('resource_id');

ResourceGateway::instance()->reorder($resourceIds);

return Response::ok()->json([]);
