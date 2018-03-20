<?php

namespace Pina\Modules\Gallery;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ContentManager;
use Pina\Modules\Images\ImageGateway;

Request::match('cp/:cp/gallery-content/:content_id');

$contentId = Request::input('content_id');
$columns = Request::input('columns');
$imageIds = Request::input('image_id');
$enabledFlags = Request::input('enabled');

$images = [];
foreach ($imageIds as $k => $imageId) {
    if (empty($imageId)) {
        continue;
    }
    
    $image = ImageGateway::instance()->find($imageId);
    $image['enabled'] = isset($enabledFlags[$k])?$enabledFlags[$k]:'N';
    $images[] = $image;
}

$params = [
    'columns' => intval($columns),
    'images' => $images,
];

if (!ContentManager::updateContent($contentId, '', json_encode($params, JSON_UNESCAPED_UNICODE))) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));