<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Paging;
use Pina\App;

Request::match('cp/:cp/resources/:resource_id/menus');

$resourceId = Request::input('resource_id');

$menus = MenuGateway::instance()
    ->select('key')
    ->select('title')
    ->leftJoin(
        MenuItemGateway::instance()->on('menu_key', 'key')->onBy('resource_id', $resourceId)->select('resource_id')
    )
    ->get();

return [
    'menus' => $menus
];
