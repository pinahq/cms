<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('carts');

$cartId = !empty($_COOKIE['cart_id'])?$_COOKIE['cart_id']:'';

return ['cart_id' => $cartId];