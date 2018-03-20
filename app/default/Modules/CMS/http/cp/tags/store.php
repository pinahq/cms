<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;

Request::match('tags');

$tag = Request::input('tag');

$parts = explode(': ', $tag, 2);
$parts = array_map('trim', $parts);
$tag = implode(': ', $parts);

$tagId = TagGateway::instance()->whereBy('tag', $tag)->id();
if (empty($tagId)) {
    $tagId = TagGateway::instance()->insertGetId(array('tag' => $tag));
}

return Response::ok()->json([
    'id' => $tagId,
    'tag' => TagGateway::instance()->whereBy('id', $tagId)->value('tag'),
]);