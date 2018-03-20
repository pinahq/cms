<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/resources/:resource_id/parents');

$resourceId = Request::input('resource_id');

$parents = ResourceGateway::instance()
    ->select('*')
    ->whereParents($resourceId)
    ->orderBy('resource_tree.length', 'desc')
    ->get();

return ['parents' => $parents];