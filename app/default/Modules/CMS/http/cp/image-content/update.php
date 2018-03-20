<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Images\Images;
use Pina\Modules\Images\ImageGateway;

Request::match('cp/:cp/image-content/:content_id');

$contentId = Request::input('content_id');
$imageId = Request::input('image_id');

$image = ImageGateway::instance()->find($imageId);

$params = Request::only('title', 'url', 'width', 'offset_left', 'offset_top', 'offset_bottom');
$params['image'] = $image;
$contentParams = json_encode($params, JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, '', $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));