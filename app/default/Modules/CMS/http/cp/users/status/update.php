<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/users/:id/status');

$id = Request::input('id');

UserGateway::instance()->whereId($id)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => UserGateway::instance()->whereId($id)->value('enabled')]);