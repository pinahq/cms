<?php

namespace Pina\Modules\Images;

use Pina\Request;
use Pina\Response;

$imageId = ImageDomain::upload();

$i = false;
if (empty($imageId)) {
    return Response::internalError();
}

$i = ImageGateway::instance()->find($imageId);
$i['url'] = ImageDomain::getFileUrl($i['filename']);

return Response::ok()->json(["image" => $i]);
