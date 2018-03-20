<?php

namespace Pina\Modules\Cart;

use Pina\Request;

Request::match('products');

$products = new Products(Request::input('sort'), Request::input('page'));
return $products->search(
    Request::input('parent_id'),
    Request::input('length'),
    Request::input('tag_id'),
    Request::input('token'),
    Request::input('sale')
);