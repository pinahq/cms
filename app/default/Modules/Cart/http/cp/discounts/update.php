<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/discounts/:id');

$id = Request::input('id');

$data = Request::only('parent_id', 'user_tag_id', 'resource_tag_id', 'percent', 'enabled');

if (empty($data['percent'])) {
    return Response::badRequest('Please enter discount', 'percent');
}

$data['resource_tag_id'] = intval($data['resource_tag_id']);
$data['user_tag_id'] = intval($data['user_tag_id']);
$data['percent'] = floatval($data['percent']);
if (empty($data['enabled']) || $data['enabled'] != 'Y') {
    $data['enabled'] = 'N';
}

if (empty($data['percent'])) {
    return Response::badRequest('Please enter discount', 'percent');
}

$id = DiscountGateway::instance()->whereId($id)->update($data);

return Response::ok();