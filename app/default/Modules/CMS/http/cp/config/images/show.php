<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Arr;
use Pina\Modules\Media\MediaGateway;

Request::match('cp/:cp/config/:namespace/images/:media_id');

$m = MediaGateway::instance()->find(Request::input('media_id'));

return ['media' => $m];
