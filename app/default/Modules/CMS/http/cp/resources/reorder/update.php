<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:parent_id/reorder/:resource_id');

$parentId = Request::input('parent_id');
$resourceId = Request::input('resource_id');
$position = Request::input('position');

$currentOrder = ResourceGateway::instance()->whereId($resourceId)->value('order');

switch ($position) {
    case 'first':
        $min = ResourceGateway::instance()->min('`order`');
        ResourceGateway::instance()
            ->whereId($resourceId)
            ->update(['order' => $min - 1]);
        break;
    
    case 'last':
        $max = ResourceGateway::instance()->max('`order`');
        
        ResourceGateway::instance()
            ->whereId($resourceId)
            ->update(['order' => $max + 1]);
        break;
    
}

return Response::ok()->json([]);