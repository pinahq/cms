<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/content/:content_id');

$contentId = Request::input('content_id');
$positionPrevId = Request::input('position_prev_id');

if ($positionPrevId === null) {
    return Response::badRequest();
}

ContentManager::insertContentAfter(
    $contentId,
    $positionPrevId
);

return Response::ok();