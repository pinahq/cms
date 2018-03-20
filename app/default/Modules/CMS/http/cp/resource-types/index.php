<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/resource-types');

$rts = ResourceTypeGateway::instance()->get();
return [
    'resource_types' => $rts,
];
