<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Paging;

Request::match('cp/:cp/tag-types/:tag_type_id/tags');

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 100);

$ts = TagGateway::instance()
        ->select('*')
        ->whereBy('tag_type_id', Request::input('tag_type_id'))
        ->leftJoin(
            ResourceGateway::instance()->on('id', 'resource_id')->selectAs('title', 'resource_title')
        )
        ->orderBy('order', 'asc')
        ->paging($paging)
        ->get();

return ['tags' => $ts, 'paging' => $paging->fetch(),];
