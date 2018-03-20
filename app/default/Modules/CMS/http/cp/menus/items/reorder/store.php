<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/menus/:key/items/:parent_id/reorder');

$key = Request::input('key');

$ids = Request::input('id');

MenuItemGateway::instance()->reorder($ids);

return Response::ok()->json([]);
