<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:resource_id/tag-types/:tag_type_id');

$tagTypeId = Request::input('tag_type_id');
$subject = Request::input('subject');

$gw = null;

switch ($subject) {
    case 'details': $gw = DetailsTagTypeGateway::instance();
        break;
    case 'list': $gw = ListTagTypeGateway::instance();
        break;
    case 'filter': $gw = FilterTagTypeGateway::instance();
        break;
}

if (empty($gw)) {
    return Response::internalError();
}

$data = ['tag_type_id' => $tagTypeId];
$gw->insertIgnore($data);

return Response::ok()->json(['relation' => $gw->whereFields($data)->exists() ? true : false]);
