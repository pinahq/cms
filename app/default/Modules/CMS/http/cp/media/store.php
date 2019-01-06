<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

use Pina\Modules\Media\Media;

$file = Media::getUploadedFile();
$file->moveToStorage();
$mediaId = $file->saveMeta();

if (empty($mediaId)) {
    return Response::internalError();
}

$m = MediaGateway::instance()->find($mediaId);
$m['url'] = Media::getUrl($m['storage'], $m['path']);

return Response::ok()->json(["media" => $m]);
