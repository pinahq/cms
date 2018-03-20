<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/menus/:key');

$key = Request::input('key');

$menu = MenuGateway::instance()->whereId($key)->update(['title' => Request::input('title')]);

return Response::ok();
