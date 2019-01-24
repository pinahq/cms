<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/list-content/:content_id');

$contentId = Request::input('content_id');

$items = Request::input('items');

foreach ($items as $k => $v)
{
    $v = array_filter($v);
    if (empty($v)) {
        unset($items[$k]);
    }
}

$data = [
    'columns' => Request::input('columns'),
    'items' => $items
];
$contentParams = json_encode($data, JSON_UNESCAPED_UNICODE);

if (!ContentManager::updateContent($contentId, '', $contentParams)) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));