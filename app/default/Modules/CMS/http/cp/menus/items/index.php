<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/menus/:key/items');

$key = Request::input('key');

$items = MenuItemGateway::instance()->whereBy('menu_key', $key)->orderBy('order', 'asc')->get();

return ['menu_items' => $items];
