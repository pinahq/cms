<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\Resource;

Request::match('cp/:cp/products/:resource_id/copy');

$resourceId = Request::input('resource_id');

$newResourceId = Resource::handleCopy($resourceId);
if (empty($newResourceId)) {
    return Response::internalError();
}

$offers = OfferGateway::instance()->whereBy('resource_id', $resourceId)->selectAllExcept('resource_id')->get();
if (!empty($offers) && is_array($offers)) {
    foreach ($offers as $offer) {
        $offerId = $offer['id'];
        unset($offer['id']);
        $newOfferId = OfferGateway::instance()->context('resource_id', $newResourceId)->insertGetId($offer);
        
        $data = OfferTagGateway::instance()->whereBy('offer_id', $offerId)->selectAllExcept('offer_id')->get();
        if (empty($data)) {
            continue;
        }
        OfferTagGateway::instance()->context('offer_id', $newOfferId)->insert($data);
    }
   
}

return Response::ok()->contentLocation(\Pina\App::link('resources/:resource_id', ['resource_id' => $newResourceId]));