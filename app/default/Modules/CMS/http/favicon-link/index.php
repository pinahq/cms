<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Media\MediaGateway;

$mediaId = Config::get(__NAMESPACE__, 'favicon');

return ['media' => MediaGateway::instance()->find($mediaId)];