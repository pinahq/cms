<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('cp/:cp/products/:resource_id/offers');

$data = Request::all();
$intKeys = ['price', 'amount', 'cost_price', 'sale_price'];
foreach ($intKeys as $key) {
    if (isset($data[$key]) && empty($data[$key])) {
        $data[$key] = 0;
    }
}

if (empty($data['enabled'])) {
    $data['enabled'] = 'N';
}

unset($data['actual_price']);

$offerId = OfferGateway::instance()->insertGetId($data);

if (empty($offerId)) {
    return Response::internalError();
}

$tags = array_unique(explode(',', Request::input('tags')));
OfferTagGateway::instance()->edit($offerId, $tags);

return Response::ok();