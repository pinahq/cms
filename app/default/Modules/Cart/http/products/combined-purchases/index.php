<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\Config;

Request::match(':resource_type/:resource_id/combined-purchases');

$resourceType = Request::input('resource_type');
$resourceId = Request::input('resource_id');
$limit = Request::input('limit');
if (empty($limit)) {
    $limit = 3;
}

$gw = ResourceGatewayExtension::instance()
    ->select('id')
    ->select('title')
    ->whereNotBy('id', $resourceId)
    ->whereResourceType($resourceType)
    ->whereEnabled();

if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
    $gw->whereInStock();
}

$gw->innerJoin(
        OfferGateway::instance()->on('resource_id', 'id')
        ->innerJoin(
            OrderOfferGateway::instance()->on('offer_id', 'id')
            ->innerJoin(
                OrderOfferGateway::instance()->alias('ordered')->on('order_id')
                ->innerJoin(
                    OfferGateway::instance()->alias('offer2')->on('id', 'offer_id')->onBy('resource_id', $resourceId)
                )
            )
        )
    )
    ->withImage()
    ->withUrl()
    ->withPrice()
    ->withListTags()
    ->limit($limit)
    ->groupBy('resource.id')
    ->orderBy('RAND()');

$rs = $gw->get();

return ['resources' => $rs];
