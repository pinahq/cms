<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('carts/:cart_id/products/:offer_id');

$cartId = Request::input('cart_id');
$offerId = Request::input('offer_id');

CartOfferGateway::instance()->whereBy('cart_id', $cartId)->whereBy('offer_id', $offerId)->delete();

return Response::ok();