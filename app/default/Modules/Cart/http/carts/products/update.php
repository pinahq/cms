<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('carts/:cart_id/products');

$cartId = Request::input('cart_id');

$amounts = Request::input('amount');

if (is_array($amounts)) {
    foreach ($amounts as $offerId => $amount) {
        $gw = CartOfferGateway::instance()
            ->whereBy('cart_id', $cartId)
            ->whereBy('offer_id', $offerId);
        if ($amount > 0) {
            $gw->update(['amount' => $amount]);
        } else {
            $gw->delete();
        }
    }
}

return Response::ok()->json([]);