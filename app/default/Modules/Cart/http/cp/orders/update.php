<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Event;

Request::match('cp/:cp/orders/:id');

$orderId = Request::input('id');

$data = Request::intersect('firstname', 'lastname', 'middlename', 'street', 'city_id', 'region_key', 'country_key', 'zip', 'phone', 'email', 'status', 'delivery_date', 'delivery_time_from', 'delivery_time_to', 'customer_comment', 'manager_comment');

if (!empty($data['delivery_date'])) {
    if (preg_match('/(\d{2})\.(\d{2})\.(\d{4}|\d{2})/si', $data['delivery_date'], $matches)) {
        list($date, $day, $month, $year) = $matches;
        $data['delivery_date'] = $year . '-' . $month . '-' . $day;
    } else {
        unset($data['delivery_date']);
    }
}

if (!empty($data['status'])) {
    $data['order_status_id'] = OrderStatusGateway::instance()->whereBy('status', $data['status'])->id();
}

if (!empty($data['city_id'])) {
    $data['city'] = CityGateway::instance()->whereId($data['city_id'])->value('city');
}

$o = OrderGateway::instance()
    ->whereId($orderId)
    ->update($data);

Event::trigger('order.updated', $orderId);

return Response::ok();