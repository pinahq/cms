<?php

namespace Pina\Modules\CMS;

use Pina\Event;
use Pina\Log;
use Pina\Mail;

try {
    $email = Event::data();
    Mail::send('subscription', ['email' => $email]);
} catch (\Exception $e) {
    Log::error('user.subscribed', $e->getMessage());
}
