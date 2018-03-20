<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceImageGateway;

Request::match('categories/:resource_id/images');

$resourceId = Request::input('resource_id');

$images = ResourceImageGateway::instance()
    ->whereBy('resource_id', $resourceId)
    ->withImage()
    ->orderBy('order', 'asc')->get();

return ['images' => $images];