<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Media\MediaGateway;
use Pina\Modules\Media\Media;

Request::match('cp/:cp/resources/:resource_id/images');

$resourceId = Request::input('resource_id');

$items = ResourceImageGateway::instance()
    ->withMedia()
    ->whereBy('resource_id', $resourceId)
    ->orderBy('order', 'asc')
    ->get();

return ['items' => $items];