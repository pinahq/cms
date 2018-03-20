<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\Event;

Request::match('cp/:cp/orders/:order_id/offers');

$orderId = Request::input('order_id');
$amounts = Request::input('amount');

if (is_array($amounts)) {
    foreach ($amounts as $offerId => $amount) {
        OrderOfferGateway::instance()->add($orderId, $offerId, $amount);
        #OrderOfferGateway::instance()->whereBy('order_id', $orderId)->whereBy('decreased', 'N')->update(['decreased' => 'Y']);
    }
}

Event::trigger('order.updated', $orderId);

return Response::found(App::link("cp/:cp/orders/:order_id", ['order_id' => $orderId, 'changed' => rand()]));