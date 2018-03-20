<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Paging;
use Pina\Modules\CMS\TagTypeGateway;

Request::match('cp/:cp/offer-tag-types');

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 100);

$gw = TagTypeGateway::instance();

if (Request::input('search')) {
    $gw->whereLike('type', '%' . Request::input('search') . '%');
}
    
$gw->paging($paging)
    ->select('id')
    ->select('type')
    ->leftJoin(
        OfferTagTypeGateway::instance()->on('tag_type_id', 'id')->selectAs('tag_type_id', 'offer_tag_type_id')
    );


$tts = $gw->get();

return ['tag_types' => $tts, 'paging' => $paging->fetch()];
