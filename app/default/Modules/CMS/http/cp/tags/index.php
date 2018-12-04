<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

Request::match('tags');

$tags = [];

$gw = TagGateway::instance()->select('id')->select('tag');

$q = Request::input('q');

if (!empty($q)) {
    $q = MySQLFullTextSearch::prepare($q);
    $gw->calculate("MATCH (tag) AGAINST('$q' IN BOOLEAN MODE) AS rel");
    $gw->orderBy('rel desc');
    $gw->having('rel > 0');

    if (Request::exists('resource_id')) {
        $gw->whereBy('resource_id', Request::input('resource_id'));
    }
    
    $tags = $gw->limit(20)->get();
}

return Response::ok()->json($tags);
