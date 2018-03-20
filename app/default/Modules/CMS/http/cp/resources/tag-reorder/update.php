<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:parent_id/tag-reorder/:resource_id');

$parentId = Request::input('parent_id');
$tagId = TagGateway::instance()->whereBy('resource_id', $parentId)->id();

$resourceId = Request::input('resource_id');
$position = Request::input('position');

$currentOrder = ResourceTagGateway::instance()
    ->whereBy('tag_id', $tagId)
    ->whereBy('resource_id', $resourceId)
    ->value('order');

switch ($position) {
    case 'first':
        $min = ResourceTagGateway::instance()
            ->whereBy('tag_id', $tagId)
            ->min('`order`');
        
        ResourceTagGateway::instance()
            ->whereBy('tag_id', $tagId)
            ->whereBy('resource_id', $resourceId)
            ->update(['order' => $min - 1]);
        break;
    
    case 'last':
        $max = ResourceTagGateway::instance()
            ->whereBy('tag_id', $tagId)
            ->max('`order`');
        
        ResourceTagGateway::instance()
            ->whereBy('tag_id', $tagId)
            ->whereBy('resource_id', $resourceId)
            ->update(['order' => $max + 1]);
        break;
    
}

return Response::ok();