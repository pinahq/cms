<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/resources/:resource_id/tags');

$resourceId = Request::input('resource_id');

$tags = ResourceTagGateway::instance()
    ->whereBy('resource_id', $resourceId)
    ->innerJoin(
        TagGateway::instance()->on('id', 'tag_id')->select('id')->select('tag')
    )->get();

return ['tags' => $tags];