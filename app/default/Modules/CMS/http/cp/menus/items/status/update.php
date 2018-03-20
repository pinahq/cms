<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/menus/:key/items/:id/status');

$key = Request::input('key');
$id = Request::input('id');

MenuItemGateway::instance()->whereBy('menu_key', $key)->whereId($id)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => MenuItemGateway::instance()->whereBy('menu_key', $key)->whereId($id)->value('enabled')]);
