<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/heading-content/:content_id');

$contentId = Request::input('content_id');
$contentText = Request::input('title');
$contentParams = json_encode(Request::only('h'), JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, $contentText, $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));