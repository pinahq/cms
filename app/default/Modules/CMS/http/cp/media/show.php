<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Media\MediaGateway;
use Pina\Modules\Media\Media;

Request::match('cp/:cp/media/:id');

$m = MediaGateway::instance()->find(Request::input('id'));
$m['url'] = Media::getUrl($m['storage'], $m['path']);
return $m;