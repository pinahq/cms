<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Event;

Request::match('cp/:cp/orders/:order_id/offers');

$orderId = Request::input('order_id');
$amounts = Request::input('amount');
$prices = Request::input('price');
$statuses = Request::input('status');

$changed = false;
if (is_array($amounts)) {
    foreach ($amounts as $orderOfferId => $amount) {
        $gw = OrderOfferGateway::instance()
            ->whereBy('order_id', $orderId)
            ->whereBy('id', $orderOfferId);
        if ($amount > 0) {
            $data = ['amount' => $amount];
            if (isset($prices[$orderOfferId])) {
                $data['actual_price'] = $prices[$orderOfferId];
            }
            if (isset($statuses[$orderOfferId])) {
                $data['order_offer_status_id'] = $statuses[$orderOfferId];
            }
            $gw->update($data);
        } else {
            $gw->delete();
        }
        $changed = true;
    }
}
if ($changed) {
    Event::trigger('order.updated', $orderId);
}

return Response::ok();