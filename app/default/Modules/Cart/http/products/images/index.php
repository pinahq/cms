<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceImageGateway;

Request::match('products/:resource_id/images');

$resourceId = Request::input('resource_id');

$images = ResourceImageGateway::instance()
    ->whereBy('resource_id', $resourceId)
    ->withMedia()
    ->orderBy('order', 'asc')->get();

return ['images' => $images];