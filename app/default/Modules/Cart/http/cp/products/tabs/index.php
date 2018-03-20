<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('cp/:cp/products/:resource_id/tabs');

$resourceId = Request::input('resource_id');

return ['offer_count' => OfferGateway::instance()->whereBy('resource_id', $resourceId)->count()];