<?php

//TODO: переделать сами картинки на массив images[]['image_id'] = XXXX

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Media\MediaGateway;

Request::match('cp/:cp/resources/:resource_id/media');

$resourceId = Request::input('resource_id');

$media = ResourceMediaGateway::instance()
    ->innerJoin(
        MediaGateway::instance()->on('id', 'media_id')
            ->select('id')
            ->select('storage')
            ->select('path')
            ->select('width')
            ->select('height')
            ->select('type')
            ->select('size')
    )
    ->whereBy('resource_id', $resourceId)
    ->orderBy('order', 'asc')
    ->get();

return ['items' => $media];