<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\TagTypeGateway;

$ts = OfferGateway::instance()
    ->innerJoin(
        OfferTagGateway::instance()->on('offer_id', 'id')
        ->innerJoin(
            TagGateway::instance()->on('id', 'tag_id')
                ->innerJoin(
                    TagTypeGateway::instance()->on('tag_type_id')
                        ->calculate('DISTINCT tag_type.tag_type_id')
                        ->select('type')
                        ->calculate("'Y' as tag_type_enabled")
                )
        )
    )
    ->get();

return ['types' => $ts];

