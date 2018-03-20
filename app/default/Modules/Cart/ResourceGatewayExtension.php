<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\ResourceGateway;

class ResourceGatewayExtension extends ResourceGateway
{

    public function withPrice()
    {
        return $this->innerJoin(
                ResourcePriceGateway::instance()
                    ->on('resource_id', 'id')
                    ->select('price')
                    ->select('actual_price')
                    ->select('sale_price')
        );
    }
    
    public function withPriceIfExists()
    {
        return $this->leftJoin(
                ResourcePriceGateway::instance()
                    ->on('resource_id', 'id')
                    ->select('price')
                    ->select('actual_price')
                    ->select('sale_price')
        );
    }

    public function whereInStock()
    {
        return $this->innerJoin(
            //ResourceAmountGateway::instance()->on('resource_id', 'id')->whereInStock()
            ResourceInStockGateway::instance()->on('resource_id', 'id')
        );
    }
    
    public function setListView()
    {
        return $this->select('id')
            ->select('title')
            ->withUrl()
            ->withListTags()
            ->withPriceIfExists()
            ->withImage()
            ->leftJoin(
                ResourceAmountGateway::instance()->on('resource_id', 'id')->select('amount')
            );
    }

}
