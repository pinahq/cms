<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/submissions/:id');

Request::filter('intval', 'id');

$id = Request::input('id');
if (empty($id)) {
    return Response::badRequest();
}

SubmissionGateway::instance()->whereId($id)->delete();

return Response::ok();