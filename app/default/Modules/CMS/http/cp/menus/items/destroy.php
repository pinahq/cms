<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/menus/:key/items/:id');

$key = Request::input('key');
$id = Request::input('id');

MenuItemGateway::instance()->whereBy('menu_key', $key)->whereId($id)->delete();

return Response::ok()->json([]);
