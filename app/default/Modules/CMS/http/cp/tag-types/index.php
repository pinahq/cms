<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/tag-types');

$ts = TagTypeGateway::instance()->get();

return ['tag_types' => $ts];
