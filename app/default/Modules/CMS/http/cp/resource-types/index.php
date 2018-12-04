<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/resource-types');

$filters = Request::intersect('tree');

$rts = ResourceTypeGateway::instance()->whereFields($filters)->get();
return [
    'resource_types' => $rts,
];
