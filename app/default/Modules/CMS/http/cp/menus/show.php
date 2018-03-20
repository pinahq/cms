<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/menus/:key');

$key = Request::input('key');

$menu = MenuGateway::instance()->find($key);

return ['menu' => $menu];
