<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\App;

Request::match('cp/:cp/offers/:id');

$offerId = Request::input('id');
$o = OfferGateway::instance()->find($offerId);
Request::result('offer', $o);