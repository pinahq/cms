<?php

namespace Pina\Modules\RetailCRM;

use Pina\Event;
use Pina\Log;

try {
    $orderId = intval(Event::data());
    
    RetailCRM::sync($orderId);
    
} catch (\Exception $e) {
    Log::error('order.sync', $e->getMessage());
}

