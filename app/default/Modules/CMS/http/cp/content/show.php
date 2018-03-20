<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/content/:id');

$id = Request::input('id');

$content = ContentGateway::instance()
        ->whereId($id)
        ->select('id')
        ->select('text')
        ->select('params')
        ->select('order')
        ->innerJoin(
            ContentTypeGateway::instance()
                ->on('id', 'content_type_id')
                ->select('type')
                ->select('title')
        )
        ->first();

if (empty($content)) {
    return Response::notFound();
}

$content['params'] = json_decode($content['params'], true);

return ['content' => $content];
