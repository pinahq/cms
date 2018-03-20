<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Arr;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceTagsGateway;
use Pina\Modules\CMS\ResourceUrlGateway;

use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Tag;

Request::match('carts/:cart_id/products');

$cartId = Request::input('cart_id');
if (!empty($cartId)) {
    $cos = CartOfferGateway::instance()
            ->select('amount')
        ->whereBy('cart_id', $cartId)
        ->calculateSubtotal()
        ->innerJoin(
            OfferGateway::instance()->on('id', 'offer_id')
                ->select('id')
                ->selectAs('amount', 'offer_amount')
                ->select('min_amount')
                ->select('fold')
                ->select('price')
                ->select('sale_price')
                ->select('actual_price')
        )
        ->innerJoin(
            ResourceGateway::instance()->on('id', 'resource_id')->select('title')->withImage()
            ->withResourceType()
        )
        ->leftJoin(
            ResourceUrlGateway::instance()->on('resource_id')->select('url')
        )
        ->leftJoin(
            ResourceTagsGateway::instance()->on('resource_id')->selectAs('tags', 'resource_tags')
        )
        ->leftJoin(
            \Pina\SQL::subquery(
                CartOfferGateway::instance()->whereBy('cart_id', $cartId)->withOfferTag()
            )->alias('offer_tag')->on('offer_id')->select('tags')
        )
        ->get();
    
    $tagTypes = TagTypeGateway::instance()->innerJoin(OrderOfferTagTypeGateway::instance()->on('tag_type_id', 'id'))->column('type');
    foreach ($cos as $k => $v) {
        $cos[$k]['tags'] = Tag::onlyTypes($v['resource_tags']."\n".$v['tags'], $tagTypes);
    }
    
    $subtotal = CartOfferGateway::instance()
        ->whereBy('cart_id', $cartId)
        ->calculatedSubtotalValue();

    $coupon = CouponGateway::instance()->select('*')
        ->whereBy('enabled', 'Y')
        ->innerJoin(
            CartCouponGateway::instance()->on('coupon')->whereBy('cart_id', $cartId)
        )
        ->first();

    $discount = 0;
    if ($coupon['absolute'] > 0) {
        $discount = $coupon['absolute'];
    } elseif ($coupon['percent'] > 0 && $coupon['percent'] <= 100) {
        $discount = round($subtotal * $coupon['percent'] / 100, 2);
    }

    $shippingSubtotal = 0;
    if (Request::input('shipping_method_id')) {
        $shippingSubtotal = Shipping::fee(Request::input('shipping_method_id'), Request::input('country_key'), Request::input('region_key'), Request::input('city_id'));
    }

    $total = $subtotal - $discount + $shippingSubtotal;
    
    return [
        'cart_offers' => $cos,
        'cart_subtotal' => $subtotal,
        'cart_amount' => array_sum(Arr::column($cos, 'amount')),
        'cart_discount' => $discount,
        'shipping_subtotal' => $shippingSubtotal,
        'total' => $total,
    ];
} else {
    return [
        'cart_offers' => [],
        'cart_subtotal' => 0,
        'cart_amount' => 0,
        'cart_discount' => 0,
        'shipping_subtotal' => 0,
        'total' => 0,
    ];
}