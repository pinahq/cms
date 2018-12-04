<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\CMS\TagGateway;

Request::match('cp/:cp/users/:id/tags');

$userId = Request::input('id');

$tags = UserTagGateway::instance()
    ->whereBy('user_id', $userId)
    ->innerJoin(
        TagGateway::instance()->on('id', 'tag_id')->select('id')->select('tag')
    )->get();

return ['tags' => $tags];