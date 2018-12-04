<?php

namespace Pina\Modules\Images;

use Pina\Request;

Request::match('cp/:cp/images/:id');

$i = ImageGateway::instance()->find(Request::input('id'));
return ['image' => $i];