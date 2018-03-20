<?php

namespace Pina\Modules\RetailCRM;

use Pina\Event;
use Pina\Log;

try {
    
    RetailCRM::update();
    
} catch (\Exception $e) {
    Log::error('order.sync', $e->getMessage());
}

