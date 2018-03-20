<?php

namespace Pina\Modules\Cart;

use Pina\Event;
use Pina\Log;
use Pina\Modules\Import\Preview;

$importId = Event::data();

try {
    $preview = new Preview($importId);
    $preview->build();
} catch (\Exception $e) {
    Log::error('catalog.build-import-preview', $e->getMessage());
}
