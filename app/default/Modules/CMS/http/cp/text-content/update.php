<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/text-content/:content_id');

$contentId = Request::input('content_id');
$contentText = Request::input('text');
$contentParams = json_encode(Request::only('width', 'offset_top', 'offset_bottom'), JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, $contentText, $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));