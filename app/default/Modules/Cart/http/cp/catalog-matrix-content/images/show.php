<?php

namespace Pina\Modules\Images;

use Pina\Request;

$i = ImageGateway::instance()
    ->whereId(Request::input('id'))
    ->selectAs('id', 'image_id')
    ->selectAs('original_id', 'image_original_id')
    ->selectAs('hash', 'image_hash')
    ->selectAs('filename', 'image_filename')
    ->selectAs('url', 'image_url')
    ->selectAs('width', 'image_width')
    ->selectAs('height', 'image_height')
    ->selectAs('type', 'image_type')
    ->selectAs('size', 'image_size')
    ->selectAs('alt', 'image_alt')
    ->first();

return ['image' => $i];