<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;
use Pina\Modules\CMS\ResourceUrlGateway;

Request::match('cp/:cp/orders/:order_id/offers');
$orderId = Request::input('order_id');

$oos = OrderOfferGateway::instance()
    ->select('id')
    ->select('amount')
    ->select('actual_price')
    ->select('price')
    ->select('sale_price')
    ->select('discount_percent')
    ->select('tags')
    ->select('title')
    ->select('image_id')
    ->select('order_offer_status_id')
    ->whereBy('order_id', $orderId)
    ->leftJoin(
        OfferGateway::instance()->on('id', 'offer_id')->selectAs('amount', 'offer_amount')
            ->leftJoin(
                ResourceUrlGateway::instance()->on('resource_id')->select('url')
            )
    )
    ->withStatus()
    ->orderBy('id ASC')
    ->get();

$statuses = OrderOfferStatusGateway::instance()->get();

return [
    'order_offers' => $oos,
    'statuses' => $statuses,
];