<?php

namespace Pina\Modules\Cart;

use Pina\Request;

if (!Request::match('cp/:cp/products/:resource_id/offers')) {
    return \Pina\Response::badRequest();
}

$os = OfferGateway::instance()
        ->select("*")
        ->withTags(OfferTagTypeGateway::instance())
        ->filters(Request::all())
        ->groupBy('offer.id')
        ->get();

return ['offers' => $os];

