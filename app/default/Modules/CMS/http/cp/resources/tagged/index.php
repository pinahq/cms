<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Paging;
use Pina\App;
use Pina\Arr;

Request::match('cp/:cp/resources/:resource_id/tagged');

$resourceId = Request::input('resource_id');
$resourceTypeId = Request::input('resource_type_id');

$tags = TagGateway::instance()->whereBy('resource_id', $resourceId)->get();
$tagIds = Arr::column($tags, 'id');

$rs = [];
$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 24);
if (!empty($tagIds)) {
    $gw = ResourceGateway::instance()->select('*')->withResourceType()->withUrl()->withChildCount()->withListTags();

    if ($resourceTypeId) {
        $gw->whereBy('resource_type_id', $resourceTypeId);
    }

    switch (Request::input('status')) {
        case 'enabled': $gw->whereBy('enabled', 'Y');
            break;
        case 'disabled': $gw->whereBy('enabled', 'N');
            break;
    }


    $gw->whereTagIds($tagIds, true);

    $gw->paging($paging, 'DISTINCT resource.id');

    $rs = $gw->get();
}

return [
    'resources' => $rs,
    'paging' => $paging->fetch(),
    'tags' => $tags,
];
