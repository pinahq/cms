<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('resources/:resource_id/tagged');

$resourceId = Request::input('resource_id');

$type = Request::input('type');

$ts = ResourceGateway::instance()->whereResourceType($type)->whereEnabled()->select('title')->withUrl()
    ->innerJoin(
        ResourceTagGateway::instance()->on('resource_id', 'id')
            ->innerJoin(
                TagGateway::instance()->on('id', 'tag_id')->whereBy('resource_id', $resourceId)
            )
    )
    ->orderBy('resource_tag.order', 'asc')
    ->get();

return ['tags' => $ts];