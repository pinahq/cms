<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Arr;
use Pina\Modules\CMS\ContentManager;
use Pina\Modules\Media\MediaGateway;

Request::match('cp/:cp/catalog-matrix-content/:content_id');

$contentId = Request::input('content_id');
$columns = Request::only('media_id', 'title', 'button', 'url', 'tags');
$params = Request::all();

$catalog = Arr::joinColumns($columns);
foreach ($catalog as $key => $value) {
    $catalog[$key]['image'] = MediaGateway::instance()->find($value['media_id']);
}

$contentParams = [
    'catalog' => $catalog,
    'width' => isset($params['width']) ? $params['width'] : '',
    'offset_top' => isset($params['offset_top']) ? $params['offset_top'] : '',
    'offset_bottom' => isset($params['offset_bottom']) ? $params['offset_bottom'] : ''
];

if (!ContentManager::updateContent($contentId, '', json_encode($contentParams, JSON_UNESCAPED_UNICODE))) {
    return Response::internalError();
}

return Response::ok()->contentLocation(\Pina\App::link('content/:content_id', ['content_id' => $contentId]));