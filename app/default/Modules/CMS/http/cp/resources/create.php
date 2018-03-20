<?php

namespace Pina\Modules\CMS;

$rts = ResourceTypeGateway::instance()->get();
return [
    'resource_types' => $rts
];