<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/menus/:key/items/:id');

$key = Request::input('key');
$id = Request::input('id');

$data = Request::only('title', 'link', 'enabled');
if ($data['enabled'] != 'Y') {
    $data['enabled'] = 'N';
}
MenuItemGateway::instance()->whereBy('menu_key', $key)->whereId($id)->update($data);

return Response::ok()->contentLocation(App::link('cp/:cp/menus/:key/items/:id', ['key' => $key, 'id' => $id]));
