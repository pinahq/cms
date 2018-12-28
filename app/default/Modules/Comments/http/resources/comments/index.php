<?php

namespace Pina\Modules\Comments;

use Pina\Request;
use Pina\Modules\CMS\UserGateway;

Request::match('resources/:resource_id/comments');

$resourceId = Request::input('resource_id');

$cs = CommentGateway::instance()
    ->select('id')
    ->select('text')
    ->select('created')
    ->select('user_id')
    ->whereBy('resource_id', $resourceId)
    ->leftJoin(
        UserGateway::instance()
            ->on('id', 'user_id')
            ->select('firstname')
            ->select('lastname')
    )
    ->get();

return ['comments' => $cs];