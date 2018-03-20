<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\App;

Request::match('cp/:cp/tags/:tag_id');

$tagId = explode(',', Request::input('tag_id'));

$tags = TagGateway::instance()->whereId($tagId)->select('id')->select('tag')->get();

return ['tags' => $tags];