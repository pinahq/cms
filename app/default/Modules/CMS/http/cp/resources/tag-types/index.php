<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Paging;

Request::match('cp/:cp/resources/:resource_id/tag-types');

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 100);

$gw = TagTypeGateway::instance();

if (Request::input('search')) {
    $gw->whereLike('type', '%' . Request::input('search') . '%');
}

$gw->paging($paging)
    ->select('id')
    ->select('type')
    ->leftJoin(
        DetailsTagTypeGateway::instance()->on('tag_type_id', 'id')->selectAs('tag_type_id', 'details_tag_type_id')
    )
    ->leftJoin(
        ListTagTypeGateway::instance()->on('tag_type_id', 'id')->selectAs('tag_type_id', 'list_tag_type_id')
    )
    ->leftJoin(
        FilterTagTypeGateway::instance()->on('tag_type_id', 'id')->selectAs('tag_type_id', 'filter_tag_type_id')
);

$tts = $gw->get();

return ['tag_types' => $tts, 'paging' => $paging->fetch()];
