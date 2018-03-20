<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/order-offer-tag-types/:tag_type_id');

$tagTypeId = Request::input('tag_type_id');

$data = ['tag_type_id' => $tagTypeId];

$gw = OrderOfferTagTypeGateway::instance();
$gw->insertIgnore($data);

return Response::ok()->json(['relation' => $gw->whereFields($data)->exists()?true:false]);