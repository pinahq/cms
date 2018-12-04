<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\TagGateway;

Request::match('cp/:cp/discounts/:id/tags');

$tag = TagGateway::instance()->find(Request::input('value'));

return [
    'tag' => $tag,
];
