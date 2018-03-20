<?php

namespace Pina\Modules\Cart;

use Pina\Log;

try {
    
    RetailCRM::update();
    
} catch (\Exception $e) {
    Log::error('order.sync', $e->getMessage());
}

