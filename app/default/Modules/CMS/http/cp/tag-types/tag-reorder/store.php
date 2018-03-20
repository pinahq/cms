<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/tag-types/:tag_type_id/tag-reorder');

$tagTypeId = Request::input('tag_type_id');

$ids = Request::input('id');

TagGateway::instance()->whereBy('tag_type_id', $tagTypeId)->reorder($ids);

return Response::ok()->json([]);
