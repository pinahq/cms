<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Media\MediaGateway;

Request::match('cp/:cp/image-content/:content_id');

$contentId = Request::input('content_id');
$mediaId = Request::input('media_id');

$image = MediaGateway::instance()->find($mediaId);

$params = Request::only('title', 'url', 'width', 'offset_left', 'offset_top', 'offset_bottom');
$params['image'] = $image;
$contentParams = json_encode($params, JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, '', $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));