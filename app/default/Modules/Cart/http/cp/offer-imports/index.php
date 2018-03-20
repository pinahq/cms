<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\Import\ImportGateway;

Request::match('cp/:cp/imports');

$is = ImportGateway::instance()->orderBy('id', 'DESC')->get();
return ['imports' => $is];