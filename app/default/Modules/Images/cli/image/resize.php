<?php

namespace Pina\Modules\Images;

use Pina\Request;
use Pina\Log;
use Pina\CLI;

$data = [
    'image_id' => 4907,
    'image_resize_width' => 320,
    'image_resize_height' => 320,
    'image_resize_trim' => 2,
];
$imageResizeId = ImageResizeGateway::instance()->whereFields($data)->id();
if (empty($imageResizeId)) {
    $imageResizeId = ImageResizeGateway::instance()->insertGetId($data);
}

echo "ID: ".$imageResizeId."\r\n";

ImageResizeFileManager::resize($imageResizeId);