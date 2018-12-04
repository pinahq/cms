<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('cp/:cp/resources/:id/tagged');

$resourceId = Request::input('id');

$tagId = Request::input('tag_id');

TagGateway::instance()->whereNotId($tagId)->whereBy('resource_id', $resourceId)->update(['resource_id' => 0]);
TagGateway::instance()->whereId($tagId)->update(['resource_id' => $resourceId]);

return Response::ok();