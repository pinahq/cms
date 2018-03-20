<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Log;
use Pina\App;
use Pina\Event;
use Pina\Modules\Import\OfferImport;

Request::match('cp/:cp/offer-imports/:import_id/offers');

$importId = Request::input('import_id');

Event::trigger('catalog.import', $importId);

return Response::ok();