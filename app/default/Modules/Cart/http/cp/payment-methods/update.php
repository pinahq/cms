<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ConfigGateway;

Request::match('cp/:cp/payment-methods/:id');

if (Request::input('enabled') != 'Y') {
    Request::set('enabled', 'N');
}

$gw = PaymentMethodGateway::instance()->whereId(Request::input('id'));
$gw->update(Request::all());

$configParams = Request::input('params');
if (!empty($configParams) && is_array($configParams)) {
    foreach ($configParams as $key => $value) {
        ConfigGateway::instance()
            ->whereNamespace(Request::input('namespace'))
            ->whereBy('key', $key)
            ->update(['value' => $value]);
    }
}

return Response::ok();