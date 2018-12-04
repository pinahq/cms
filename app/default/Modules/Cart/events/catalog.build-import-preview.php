<?php

namespace Pina\Modules\Cart;

use Pina\Event;
use Pina\Log;
use Pina\Modules\CMS\ImportPreview;

$importId = Event::data();

try {
    $preview = new ImportPreview($importId, new ImportOfferSchema);
    $preview->build();
} catch (\Exception $e) {
    Log::error('catalog.build-import-preview', $e->getMessage());
}
