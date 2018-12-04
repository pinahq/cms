<?php

namespace Pina\Modules\Cart;

use Pina\Event;

$importId = Event::data();

try {
    $import = new OfferImport($importId, new ImportOfferSchema);
    $import->import();
} catch (\Exception $e) {
    \Pina\Log::error('catalog.import', $e->getMessage());
}