<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/resources/:id/parents');

if (!Request::input('async') && !Request::input('page')) {
    $count = ResourceGateway::instance()->innerJoin(
            ResourceTypeGateway::instance()->on('id', 'resource_type_id')->onBy('tree', 'Y')
        )->count();

    if ($count > 100) {
        Request::set('async', true);
    }
}

if (Request::input('async')) {
    $rs = [];
    $parentId = Request::input('parent_id');
    if (empty($parentId) && Request::input('id')) {
        $parentId = ResourceGateway::instance()->whereId(Request::input('id'))->value('parent_id');
    }
    $rs = ResourceGateway::instance()->treeView()->whereBy('id', $parentId)->get();
} else {
    $paging = Request::isExternalRequest() ? new \Pina\Paging(Request::input('page'), 100) : null;
    $rs = ResourceGateway::instance()->findList(0, Request::input('id'), $paging, Request::input('search'));
    if (Request::input('page') == 1) {
        $rs = array_merge([['id' => 0, 'title' => '/']], $rs);
    }
}


return ['resources' => $rs, 'paging' => $paging ? $paging->fetch() : null];
