<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/menus/:key/items');

$key = Request::input('key');

$data = Request::only('link', 'title', 'enabled');
if ($data['enabled'] != 'Y') {
    $data['enabled'] = 'N';
}
$id = MenuItemGateway::instance()->context('menu_key', $key)->insertGetId($data);

return Response::ok()->contentLocation(App::link('cp/:cp/menus/:key/items/:id', ['key' => $key, 'id' => $id]));
