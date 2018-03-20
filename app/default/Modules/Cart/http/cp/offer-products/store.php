<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceTypeGateway;

$offerIds = OfferGateway::instance()
        ->whereBy('resource_id', 0)
        ->column('id');

$tagTypeIds = Request::input('tag_types');

$productResourceTypeId = ResourceTypeGateway::instance()->whereBy('type', 'products')->id();

foreach ($offerIds as $offerId) {
    echo $offerId."\r\n";
    $tagIds = OfferTagGateway::instance()
        ->whereBy('offer_id', $offerId)
        ->innerJoin(TagGateway::instance()->on('id', 'tag_id')->whereBy('tag_type_id', $tagTypeIds))
        ->column('tag_id');
    
    $tagIds = array_unique($tagIds);
    
    if (empty($tagIds)) {
        continue;
    }
    
    $resourceId = ResourceGateway::instance()
        ->whereBy('resource_type_id', $productResourceTypeId)
        ->whereTagIds($tagIds)
        ->value('id');
    
    if (empty($resourceId)) {
        $resourceId = ResourceGateway::instance()->insertGetId(array('resource_type_id' => $productResourceTypeId));
        $links = array();
        foreach ($tagIds as $tagId) {
            $links[] = array('resource_id' => $resourceId, 'tag_id' => $tagId);
        }
        ResourceTagGateway::instance()->insert($links);
    }
    
    if ($resourceId) {
        OfferGateway::instance()->whereBy('id', $offerId)->update(array('resource_id' => $resourceId));
    }
    
    #ResourceGateway::instance()
}

return Response::ok();