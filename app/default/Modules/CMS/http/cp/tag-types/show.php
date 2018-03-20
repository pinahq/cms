<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/tag-types/:id');

$id = Request::input('id');

$t = TagTypeGateway::instance()->find($id);

return ['tag_type' => $t];
