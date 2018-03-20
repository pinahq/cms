<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\TagGateway;

Request::match('cp/:cp/offers/:offer_id/tags');

$offerId = Request::input('offer_id');

$tags = OfferTagGateway::instance()
    ->whereBy('offer_id', $offerId)
    ->innerJoin(
        TagGateway::instance()->on('id', 'tag_id')->select('id')->select('tag')
    )->get();

return ['tags' => $tags];