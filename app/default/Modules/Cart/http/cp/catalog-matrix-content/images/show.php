<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Media\MediaGateway;

return ['image' => MediaGateway::instance()->find(Request::input('id'))];