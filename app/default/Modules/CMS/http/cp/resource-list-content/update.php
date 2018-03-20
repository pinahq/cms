<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resource-list-content/:content_id');

$contentId = Request::input('content_id');
$resourceTypeId = Request::input('resource_type_id');
$type = ResourceTypeGateway::instance()->find($resourceTypeId);
$data = [
    'type_id' => $type['id'],
    'type' => $type['type'],
    'parent_id' => Request::input('parent_id'),
    'length' => Request::input('length'),
];
$contentParams = json_encode($data, JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, '', $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));