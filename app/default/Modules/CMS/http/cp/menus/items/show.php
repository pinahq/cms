<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/menus/:key/items/:id');

$key = Request::input('key');
$id = Request::input('id');

$item = MenuItemGateway::instance()->whereBy('menu_key', $key)->find($id);

return ['menu_item' => $item];
