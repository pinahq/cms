<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('content/:id');

$id = Request::input('id');

$content = ContentGateway::instance()->whereId($id)->select('*')
    ->innerJoin(
        ContentTypeGateway::instance()->on('id', 'content_type_id')->select('type')
    )
    ->first();

if (empty($content)) {
    return Response::notFound();
}

$content['params'] = json_decode($content['params'], true);

return ['content' => $content];
