<?php

namespace Pina\Modules\CMS;

use Pina\Event;
use Pina\Log;
use Pina\Mail;

try {
    $submissionId = Event::data();
    Mail::send('submission', array("id" => $submissionId));
} catch (\Exception $e) {
    Log::error('submission.created', $e->getMessage());
}
