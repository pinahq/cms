<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Arr;

use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceGateway;

#error_reporting(E_ALL);
#ini_set("display_errors", "true");

Request::match('products/:id/brand');

$id = Request::input('id');

$tag = TagGateway::instance()
    ->select('id')
    ->select('tag')
    ->innerJoin(
        ResourceTagGateway::instance()->on('tag_id', 'id')->onBy('resource_id', $id)
    )
    ->innerJoin(
        TagTypeGateway::instance()->on('id', 'tag_type_id')->onBy('type', 'Бренд')
    )
    ->withResourceUrl()
    ->leftJoin(
        ResourceGateway::instance()->on('id', 'resource_id')->withImage()
    )
    ->first();

return ['tag' => $tag];