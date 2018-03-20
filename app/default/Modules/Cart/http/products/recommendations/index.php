<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\Config;

Request::match(':resource_type/:resource_id/recommendations');

$resourceType = Request::input('resource_type');
$limit = Request::input('limit');
if (empty($limit)) {
    $limit = 3;
}

$gw = ResourceGatewayExtension::instance()
    ->select('id')
    ->select('title')
    ->whereResourceType($resourceType)
    ->whereEnabled();
    
if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
    $gw->whereInStock();
}

$gw->withImage()
    ->withUrl()
    ->withPrice()
    ->withListTags()
    ->limit($limit)
    ->orderBy('RAND()');

$rs = $gw->get();

return ['resources' => $rs];