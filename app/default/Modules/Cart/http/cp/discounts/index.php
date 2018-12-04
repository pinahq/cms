<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\TagGateway;

Request::match('cp/:cp/discounts');

$ds = DiscountGateway::instance()
    ->select('*')
    ->leftJoin(
        ResourceGateway::instance()->on('id', 'parent_id')->selectAs('title', 'resource_title')
    )
    ->leftJoin(
        TagGateway::instance()->alias('user_tag')->on('id', 'user_tag_id')->selectAs('tag', 'user_tag')
    )
    ->leftJoin(
        TagGateway::instance()->alias('resource_tag')->on('id', 'resource_tag_id')->selectAs('tag', 'resource_tag')
    )
    ->get();

return ['discounts' => $ds];