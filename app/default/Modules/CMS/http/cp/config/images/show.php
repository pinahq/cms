<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Arr;
use Pina\Modules\Images\ImageGateway;

Request::match('cp/:cp/config/:namespace/images/:image_id');

$i = ImageGateway::instance()->find(Request::input('image_id'));

return ['image' => $i];
