<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Arr;
use Pina\Modules\CMS\ContentManager;
use Pina\Modules\Images\ImageGateway;

Request::match('cp/:cp/catalog-matrix-content/:content_id');

$contentId = Request::input('content_id');
$columns = Request::only('image_id', 'title', 'button', 'url', 'tags');
$params = Request::all();

$catalog = Arr::joinColumns($columns);
foreach ($catalog as $key => $value) {
    $image = ImageGateway::instance()
        ->whereId($value['image_id'])
        ->selectAs('id', 'image_id')
        ->selectAs('original_id', 'image_original_id')
        ->selectAs('hash', 'image_hash')
        ->selectAs('filename', 'image_filename')
        ->selectAs('url', 'image_url')
        ->selectAs('width', 'image_width')
        ->selectAs('height', 'image_height')
        ->selectAs('type', 'image_type')
        ->selectAs('size', 'image_size')
        ->selectAs('alt', 'image_alt')
        ->first();
    $catalog[$key] = array_merge($catalog[$key], $image);
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