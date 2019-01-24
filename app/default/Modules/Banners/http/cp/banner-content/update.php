<?php

namespace Pina\Modules\Banners;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ContentManager;
use Pina\Modules\Media\MediaGateway;

Request::match('cp/:cp/banner-content/:content_id');

$contentId = Request::input('content_id');
$mediaIds = Request::input('media_id');
$linkUrls = Request::input('link_url');
$enabledFlags = Request::input('enabled');

$images = [];
foreach ($mediaIds as $k => $mediaId) {
    if (empty($mediaId)) {
        continue;
    }
    
    $image = MediaGateway::instance()->find($mediaId);
    $image['link_url'] = isset($linkUrls[$k])?$linkUrls[$k]:'';
    $image['enabled'] = isset($enabledFlags[$k])?$enabledFlags[$k]:'N';
    $images[] = $image;
}

$params = [
    'images' => $images,
];

if (!ContentManager::updateContent($contentId, '', json_encode($params, JSON_UNESCAPED_UNICODE))) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));