<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/content/create');

$cts = ContentTypeGateway::instance()->get();

return ['content_types' => $cts];