<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Paging;
use Pina\SQL;
use Pina\Arr;

Request::match('cp/:cp/resources');

$resourceTypeId = Request::input('resource_type_id');
$parentId = Request::input('parent_id');

$searchGW = ResourceTreeGateway::instance()->select('resource_id');

if ($resourceTypeId) {
    $searchGW->whereBy('resource_type_id', $resourceTypeId);
}

switch (Request::input('status')) {
    case 'enabled': $searchGW->whereBy('resource_enabled', 'Y'); break;
    case 'disabled': $searchGW->whereBy('resource_enabled', 'N'); break;
}


$searchGW->whereBy('resource_parent_id', $parentId);

if (Request::input('length')) {
    $searchGW->whereBy('length', intval(Request::input('length')));
}
$searchGW->groupBy('resource_tree.resource_order');
$searchGW->groupBy('resource_tree.resource_id');
$searchGW->orderBy('resource_tree.resource_order', 'asc');
$searchGW->orderBy('resource_tree.resource_id', 'desc');


if (Request::input('search') || Request::input('tag_id') || Request::input('tag')) {
    $searchGW->leftJoin(ResourceGateway::instance()->on('id', 'resource_id'));
    $searchGW->leftJoin(ResourceTextGateway::instance()->on('resource_id'));

    $tagGW = TagGateway::instance()->alias('token_tag');
    if (Request::input('tag_id')) {
        $tagId = intval(Request::input('tag_id'));
        $tagGW->whereBy('id', $tagId);
    } elseif (Request::input('tag') && Request::input('tag_type_id')) {
        $tag = MySQLFullTextSearch::prepare(Request::input('tag'));
        $tagGW->where("MATCH(token_tag.tag) AGAINST('$tag' IN BOOLEAN MODE)");
        $tagGW->whereBy('tag_type_id', Request::input('tag_type_id'));
    }

    $searchGW->leftJoin(
        ResourceTagGateway::instance()->on('resource_id')->alias('token_resource_tag')
            ->leftJoin($tagGW->on('id', 'tag_id'))
    );

    if (Request::input('search')) {
        $search = MySQLFullTextSearch::prepare(Request::input('search'));
        $condition = "MATCH(resource.title) AGAINST('$search' IN BOOLEAN MODE)";
        $condition .= " OR MATCH(resource_text.text) AGAINST('$search' IN BOOLEAN MODE)";
        $condition .= " OR MATCH(token_tag.tag) AGAINST('$search' IN BOOLEAN MODE)";
        $searchGW->where($condition);
    }
}

$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 24);
$searchGW->paging($paging, 'DISTINCT resource_tree.resource_id');

$query = SQL::subquery($searchGW->select('resource_order'))->alias('search');
$newQuery = SQL::subquery(
    $query->leftJoin(
        ResourceTreeGateway::instance()->alias('child_count')->on('resource_parent_id', 'resource_id')
    )
    ->select('resource_id')
    ->select('resource_order')
    ->calculate('count(child_count.resource_id) as child_count')
    ->groupBy('search.resource_id')
)->alias('new_search')
->select('child_count');

$newQuery->innerJoin(
    ResourceGateway::instance()->on('id', 'resource_id')->select('*')->withResourceType('cp_pattern')->withUrl()->withListTags()
);
$newQuery->orderBy('new_search.resource_order', 'asc');

$rs = $newQuery->get();

return [
    'resources' => $rs,
    'paging' => $paging->fetch(),
];
