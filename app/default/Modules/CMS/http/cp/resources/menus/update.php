<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:resource_id/menus/:key');

$key = Request::input('key');
$resourceId = Request::input('resource_id');

$title = ResourceGateway::instance()->whereId($resourceId)->value('title');

if (Request::input('enabled') == 'Y') {
    MenuItemGateway::instance()->insertIgnore([
        'menu_key' => $key,
        'title' => $title,
        'link' => '',
        'resource_id' => $resourceId,
    ]);
} else {
    MenuItemGateway::instance()->whereBy('menu_key', $key)->whereBy('resource_id', $resourceId)->delete();
}

return Response::ok()->json([
        'enabled' => MenuItemGateway::instance()->whereBy('menu_key', $key)->whereBy('resource_id', $resourceId)->exists() ? 'Y' : 'N'
    ]);
