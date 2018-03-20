<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:id/tagged');

$resourceId = Request::input('id');

$tagIds = array_unique(explode(',', Request::input('tags')));

TagGateway::instance()->whereNotId($tagIds)->whereBy('resource_id', $resourceId)->update(['resource_id' => 0]);
TagGateway::instance()->whereId($tagIds)->update(['resource_id' => $resourceId]);

return Response::ok();