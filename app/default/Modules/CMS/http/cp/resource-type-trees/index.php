<?php

namespace Pina\Modules\CMS;

use Pina\Request;

$types = ResourceTypeGateway::instance()->whereBy('tree', 'Y')->get();

$activeId = ResourceGateway::instance()->whereId(Request::input('parent_id'))->value('resource_type_id');

return [
    'types' => $types,
    'active_id' => $activeId
];