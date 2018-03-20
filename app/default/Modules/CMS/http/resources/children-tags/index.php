<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('resources/:resource_id/children-tags');

$resourceId = Request::input('resource_id');

$tagType = Request::input('tag_type');

$tagTypeId = TagTypeGateway::instance()->whereBy('tag_type', $tagType)->value('tag_type_id');

$ts = TagGateway::instance()->calculate('DISTINCT tag.id')->select('tag')
    ->innerJoin(
        ResourceTagGateway::instance()->on('tag_id', 'id')->onBy('tag_type_id', $tagTypeId)
            ->innerJoin(
                ResourceTreeGateway::instance()->on('resource_id')->onBy('resource_parent_id', $resourceId)
            )
    )
    ->leftJoin(
        ResourceUrlGateway::instance()->on('resource_id')->select('url')
    )
    ->get();

return ['tags' => $ts];
