<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/content-types/:content_type');

$contentType = Request::input('content_type');

$ct = ContentTypeGateway::instance()->whereBy('type', $contentType)->first();

return ['content_type' => $ct];