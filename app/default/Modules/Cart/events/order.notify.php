<?php

namespace Pina\Modules\Cart;

use Pina\Event;
use Pina\Mail;

$orderId = intval(Event::data());

Mail::send('customer', array("order_id" => $orderId));
Mail::send('merchant', array("order_id" => $orderId));
