<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/discounts');

$data = Request::all();

if (empty($data['percent'])) {
    return Response::badRequest('Please enter discount', 'percent');
}

$data['resource_tag_id'] = intval($data['resource_tag_id']);
$data['user_tag_id'] = intval($data['user_tag_id']);
$data['percent'] = floatval($data['percent']);
if (empty($data['enabled']) || $data['enabled'] != 'Y') {
    $data['enabled'] = 'N';
}

$id = DiscountGateway::instance()->insertGetId($data);

return Response::ok()->contentLocation(\Pina\App::link('cp/:cp/discounts/:id', ['id' => $id]));