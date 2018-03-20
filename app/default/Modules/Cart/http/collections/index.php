<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceGateway;

Request::match('collections');

$rs = ResourceGateway::instance()->select('*')
    ->whereEnabled()
    ->whereBy('resource_type_id', Request::input('resource_type_id'))
    ->whereChildren(0, 1)
    ->withUrl()
    ->get();

return ['resources' => $rs];