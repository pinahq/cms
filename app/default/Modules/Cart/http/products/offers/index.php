<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Arr;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\ResourceTypeGateway;
use Pina\Modules\CMS\TagTypeGateway;

Request::match('products/:resource_id/offers');

$resourceId = Request::input('resource_id');

$cartId = !empty($_COOKIE['cart_id']) ? $_COOKIE['cart_id'] : '';
$gw = OfferGateway::instance()
        ->select('id')
        ->select('price')
        ->select('sale_price')
        ->select('actual_price')
        ->select('amount')
        ->select('min_amount')
        ->select('fold')
        ->whereBy('resource_id', $resourceId)
        ->whereBy('enabled', 'Y')
        ->where('offer.amount > 0')
        ->withCartOfferAmount($cartId)
        ->calculate('(offer.amount - LEAST(offer.amount,IFNULL(cart_offer.amount,0))) as available_amount')
        ->having('available_amount > 0')
        ->groupBy('offer.id');
    
if (Request::input('display') === 'table') {
    $gw->withConcatUniqueTags($resourceId);
    $os = $gw->get();
    return ['offers' => $os];
}


$gw->leftJoin(OfferTagGateway::instance()->on('offer_id', 'id')->calculate("GROUP_CONCAT(tag_id ORDER BY tag_id ASC SEPARATOR ',') as tag_ids"));
$os = $gw->get();

foreach ($os as $k => $o) {
    $os[$k]['tag_ids'] = explode(',', $o['tag_ids']);
}

$offerIds = Arr::column($os, 'id');

$allOfferIds = OfferGateway::instance()->whereBy('resource_id', $resourceId)->column('id');

//TODO объединить два запроса в один и понять, нужно ли выводить 
//для справочной информации теги товаров, которых нет на складе
$tagTypeIds = array_column(OfferTagTypeGateway::instance()
        ->innerJoin(OfferTagGateway::instance()->alias('offer_tag')->on('tag_type_id')->onBy('offer_id', $allOfferIds))
        ->calculate('DISTINCT offer_tag.tag_type_id as id')
        ->get(), 'id');

$tags = OfferTagGateway::instance()
    ->whereBy('offer_id', $offerIds)
    ->innerJoin(
        TagGateway::instance()->on('id', 'tag_id')
        ->select('id')
        ->select('tag')
        ->whereBy('tag_type_id', $tagTypeIds)
        ->innerJoin(
            TagTypeGateway::instance()->on('id', 'tag_type_id')->select('type')
        )
    )
    ->groupBy('tag_id')
    ->orderBy('tag.order', 'asc')
    ->get();

$tagGroups = Arr::group($tags, 'type');
return [
    'offers' => $os,
    'tag_groups' => $tagGroups
];
