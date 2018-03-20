<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('cp/:cp/products/:resource_id/offers/:id');

$offerId = Request::input('id');
$resourceId = Request::input('resource_id');
$o = OfferGateway::instance()->whereBy('resource_id', $resourceId)->find($offerId);
return ['offer' => $o];