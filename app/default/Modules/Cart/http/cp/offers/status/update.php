<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/offers/:offer_id/status');

$offerId = Request::input('offer_id');

OfferGateway::instance()->whereId($offerId)->update(Request::only('enabled'));

return Response::ok()->json(['enabled' => OfferGateway::instance()->whereId($offerId)->value('enabled')]);