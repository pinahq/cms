<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('resources/:id/parents');

$id = Request::input('id');

$parents = ResourceGateway::instance()
    ->select('*')
    ->withResourceType()
    ->withUrl()
    ->whereParents($id)
    ->orderBy('resource_tree.length', 'desc')
    ->get();

return ['parents' => $parents];