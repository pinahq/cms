<?php

namespace Pina\Modules\CMS;

use Pina\Arr;
use Pina\Request;

Request::match('cp/:cp/resource-type-trees/:id');

$resourceParentId = Request::input('parent_id');

$parentIds = ResourceTreeGateway::instance()->whereBy('resource_id', Request::input('parent_id'))->column('resource_parent_id');

$resourceTrees = ResourceTreeGateway::instance()
	->innerJoin(ResourceGateway::instance()->on('id', 'resource_id')->select('title'))
	->whereBy('resource_type_id', Request::input('id'))
	->whereBy('length', '1')
	->select('resource_id')
	->select('resource_parent_id')
	->get();
$resourceTrees = Arr::group($resourceTrees, 'resource_parent_id');

$url = Request::input('url');

$parsed = parse_url($url);
$query = [];
parse_str($parsed['query'], $query);

$createTree = function ($parentId) use (&$createTree, &$resourceTrees, &$url, $parentIds, $resourceParentId, $parsed, $query) {
	$tree = [];
	if (isset($resourceTrees[$parentId])) {
		foreach ($resourceTrees[$parentId] as $child) {
            $query['parent_id'] = $child['resource_id'];
			$tree[] = [
				'id' => $child['resource_id'],
				'text' => $child['title'],
                'state' => ['opened' => in_array($child['resource_id'], $parentIds)?true:false, 'selected' => $child['resource_id'] == $resourceParentId],
				'url' => \Pina\App::link($parsed['path'], $query),
				'children' => $createTree($child['resource_id'])
			];
		}
	}

	return $tree;
};

$tree = $createTree(0);
return ['data' => json_encode($tree, JSON_UNESCAPED_UNICODE)];
