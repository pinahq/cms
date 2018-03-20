<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Tag;

Request::match('users/:user_id/orders/:order_id');
$userId = Request::input('user_id');
$orderId = Request::input('order_id');

$o = OrderGateway::instance()->whereBy('user_id', $userId)
    ->select('*')
    ->leftJoin(
        OrderStatusGateway::instance()->on('id', 'order_status_id')->selectAs('title', 'order_status_title')
    )
    ->leftJoin(
        \Pina\Modules\Regions\CountryGateway::instance()->on('key', 'country_key')->select('country')
    )
    ->leftJoin(
        \Pina\Modules\Regions\RegionGateway::instance()->on('key', 'region_key')->on('country_key', 'country.key')->select('region')
    )
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