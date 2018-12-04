<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ImportGateway;

Request::match('cp/:cp/imports');

$is = ImportGateway::instance()->orderBy('id', 'DESC')->get();
return ['imports' => $is];