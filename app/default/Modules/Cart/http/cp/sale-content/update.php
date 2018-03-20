<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/sale-content/:content_id');

$contentId = Request::input('content_id');
$data = [
    'parent_id' => Request::input('parent_id'),
    'length' => Request::input('length'),
];
$contentParams = json_encode($data, JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, '', $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));