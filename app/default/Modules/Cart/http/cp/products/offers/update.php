<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;

if (!Request::match('cp/:cp/products/:resource_id/offers/:id')) {
    if (!Request::match('cp/:cp/products/:resource_id/offers')) {
        return Response::badRequest();
    }
}

$offerId = Request::input('id');
$resourceId = Request::input('resource_id');

$data = Request::all();
$intKeys = ['price', 'amount', 'fold', 'min_amount', 'cost_price', 'sale_price'];
foreach ($intKeys as $key) {
    if (isset($data[$key]) && empty($data[$key])) {
        $data[$key] = 0;
    }
}
if (isset($data['min_amount']) && empty($data['min_amount'])) {
    $data['min_amount'] = 1;
}
if (isset($data['fold']) && empty($data['fold'])) {
    $data['fold'] = 1;
}

if (empty($data['enabled'])) {
    $data['enabled'] = 'N';
}

unset($data['actual_price']);

$gw = OfferGateway::instance()->whereBy('resource_id', $resourceId);
if (!empty($offerId)) {
    $gw->whereId($offerId);
} else {
    unset($data['enabled']);
}
$gw->update($data);

if (!empty($offerId)) {
    $tags = array_unique(explode(',', Request::input('tags')));
    OfferTagGateway::instance()->edit($offerId, $tags);
}

return Response::ok();
