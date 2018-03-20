<?php

//TODO: переделать сами картинки на массив images[]['image_id'] = XXXX

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Images\ImageGateway;

Request::match('cp/:cp/resources/:resource_id/images');

$resourceId = Request::input('resource_id');

$images = ResourceImageGateway::instance()
    ->innerJoin(
        ImageGateway::instance()->on('id', 'image_id')
            ->select('id')
            ->select('filename')
            ->select('url')
            ->select('width')
            ->select('height')
            ->select('type')
            ->select('size')
            ->select('alt')
    )
    ->whereBy('resource_id', $resourceId)
    ->orderBy('order', 'asc')
    ->get();

return ['images' => $images];