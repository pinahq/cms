<?php

namespace Pina\Modules\CMS;

use Pina\Response;
use Pina\Modules\Media\Media;
use Pina\Modules\Media\MediaGateway;

try {
    $file = Media::getUploadedFile();
    if (!$file->isMimeType('image/*')) {
        return Response::badRequest('Wrong image type');
    }
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
