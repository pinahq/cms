<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Arr;
use Pina\DB;

if (Request::input('display') == 'selector') {
    $tagTypes = TagTypeGateway::instance()->orderBy('type', 'asc')->get();
    $tagTypes = Arr::column($tagTypes, 'type', 'id');
    $tagTypes = [''] + $tagTypes;
    return ['tag_types' => $tagTypes];
} else {
    $q = Request::input('q');
    $tags = [];
    if (strlen($q) > 2) {
        $gw = TagGateway::instance()->select('id')->select('tag');
        $gw->whereBy('tag_type_id', Request::input('tag_type_id'));

        $prepareSearchPhrase = MySQLFullTextSearch::prepare($q);
        $gw->calculate("MATCH (tag) AGAINST('$prepareSearchPhrase' IN BOOLEAN MODE) AS rel");
        
        $gw->orderBy('rel desc');
        $gw->having('rel > 0');
        $gw->limit(10);

        $tags = $gw->limit(10)->get();
        if (is_array($tags) && !empty($tags)) {
            $tagTypeTitle = TagTypeGateway::instance()->whereBy('id', Request::input('tag_type_id'))->value('type');
            $tags = array_map(function ($v) use (&$tagTypeTitle) {
                $v['tag'] = str_replace("$tagTypeTitle: ", '', $v['tag']);
                return $v;
            }, $tags);
        }

    }
    return Response::ok()->json($tags);
}
