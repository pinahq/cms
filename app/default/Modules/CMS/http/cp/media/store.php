<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Media\Media;
use Pina\Modules\Media\MediaGateway;

try {
    $file = Media::getUploadedFile();
    $file->moveToStorage();
    $mediaId = $file->saveMeta();
} catch (\RuntimeException $e) {
    return Response::internalError($e->getMessage());
}

if (empty($mediaId)) {
    return Response::internalError();
}

$m = MediaGateway::instance()->find($mediaId);
$m['url'] = Media::getUrl($m['storage'], $m['path']);

return Response::ok()->json($m);
