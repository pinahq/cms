<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/content');

$contentId = ContentManager::addContent(
    Request::input('resource_id'),
    Request::input('slot'),
    '',
    json_encode([]),
    Request::input('type')
);
if (empty($contentId)) {
    return Response::internalError();
}

return Response::found(\Pina\App::link('cp/:cp/content/:content_id', ['content_id' => $contentId, 'display' => 'wrapper']));