<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\ResourceTreeGateway;

class ResourceTreeGatewayExtension extends ResourceTreeGateway
{

    public function withPrice()
    {
        return $this->innerJoin(
                ResourcePriceGateway::instance()
                    ->on('resource_id')
                    ->select('price')
                    ->select('actual_price')
                    ->select('sale_price')
        );
    }
    
    public function withPriceIfExists()
    {
        return $this->leftJoin(
                ResourcePriceGateway::instance()
                    ->on('resource_id')
                    ->select('price')
                    ->select('actual_price')
                    ->select('sale_price')
        );
    }

    public function whereInStock()
    {
        return $this->innerJoin(
            //ResourceAmountGateway::instance()->on('resource_id')->whereInStock()
            ResourceInStockGateway::instance()->on('resource_id')
        );
    }

}
