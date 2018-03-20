<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceTagsGateway;
use Pina\Modules\CMS\Config;

Request::match('catalog-matrix-content/:content_id/products');

$tags = Request::input('tags');
$products = [];
if (!empty($tags)) {

    $tagIds = explode(',', $tags);

    if (empty($tagIds) || !is_array($tagIds) || count($tagIds) == 0) {
        return;
    }

    $gw = ResourceGatewayExtension::instance()->select('*');
    if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
        $gw->whereInStock();
    }

    $gw->whereEnabled()->withUrl()->withPrice()->whereTagIds($tagIds);
    $gw->leftJoin(
        ResourceTagsGateway::instance()->on('resource_id', 'id')->select('tags')
    );
    $products = $gw->limit(2)->get();
}

return ['products' => $products];
