<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Mail;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Tag;

$orderId = Request::input('order_id');

$o = OrderGateway::instance()
    ->select('*')
    ->withStatus()
    ->withCountryAndRegion()
    ->find($orderId);


$oos = OrderOfferGateway::instance()->whereBy('order_id', $orderId)->get();

$tagTypes = TagTypeGateway::instance()->innerJoin(OrderOfferTagTypeGateway::instance()->on('tag_type_id', 'id'))->column('type');
foreach ($oos as $k => $v) {
    $oos[$k]['tags'] = Tag::onlyTypes($oos[$k]['tags'], $tagTypes);
}

Mail::to($o['email'], trim($o['firstname'] . ' ' . $o['lastname']));

return [
    'order' => $o,
    'offers' => $oos,
    'host' => \Pina\App::host(),
];
