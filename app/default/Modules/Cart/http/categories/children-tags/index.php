<?php

namespace Pina\Modules\Cart;

use Pina\Request;

use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceTreeGateway;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceUrlGateway;
use Pina\Modules\CMS\Config;

Request::match('categories/:resource_id/children-tags');

$resourceId = Request::input('resource_id');

$tagType = Request::input('tag_type');

$resourceGw = ResourceTagGateway::instance()->on('tag_id', 'id')
    ->innerJoin(
        ResourceTreeGateway::instance()->on('resource_id')->onBy('resource_parent_id', $resourceId)
    );

if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
    $resourceGw->innerJoin(
        ResourceInStockGateway::instance()->on('resource_id')
    );
}

$ts = TagTypeGateway::instance()->whereBy('type', $tagType)
        ->innerJoin(
            TagGateway::instance()->on('tag_type_id', 'id')->calculate('DISTINCT tag.id')->select('tag')
            ->innerJoin(
                $resourceGw
            )
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'resource_id')->onBy('enabled', 'Y')
            )
            ->innerJoin(
                ResourceUrlGateway::instance()->on('resource_id')->select('url')
            )
    )
    ->get();

return ['tags' => $ts];