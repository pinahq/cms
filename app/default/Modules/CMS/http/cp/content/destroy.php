<?php

namespace Pina\Modules\Banners;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ContentManager;

Request::match('cp/:cp/content/:content_id');

ContentManager::removeContent(Request::input('content_id'));

return Response::ok();