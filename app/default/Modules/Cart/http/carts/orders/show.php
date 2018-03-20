<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Tag;

Request::match('carts/:cart_id/orders/:order_id');
$cartId = Request::input('cart_id');
$orderId = Request::input('order_id');

$o = OrderGateway::instance()->whereBy('cart_id', $cartId)
    ->select('*')
    ->withStatus()
    ->withCountryAndRegion()
    ->find($orderId);

if (empty($o)) {
    return Response::notFound();
}

$tagTypes = TagTypeGateway::instance()->innerJoin(OrderOfferTagTypeGateway::instance()->on('tag_type_id', 'id'))->column('type');
$oos = OrderOfferGateway::instance()->whereBy('order_id', $orderId)->get();
foreach ($oos as $k => $v) {
    $oos[$k]['tags'] = Tag::onlyTypes($oos[$k]['tags'], $tagTypes);
}

return [
    'order' => $o,
    'offers' => $oos,
];