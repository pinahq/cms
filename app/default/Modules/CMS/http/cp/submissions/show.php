<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/submissions/:id');

$id = Request::input('id');

$s = SubmissionGateway::instance()->find($id);
if (!empty($s['data'])) {
    $s['data'] = json_decode($s['data'], true);
}

return ['submission' => $s];
