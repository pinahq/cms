<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Images\ImageDomain;
use Pina\Modules\Images\ImageGateway;

$imageId = Config::get(__NAMESPACE__, 'favicon');

$image = ImageGateway::instance()->find($imageId);

return [
    'type' => !empty($image['type'])?$image['type']:'',
    'url' => !empty($image)?ImageDomain::getFileUrl($image['filename']):'',
];