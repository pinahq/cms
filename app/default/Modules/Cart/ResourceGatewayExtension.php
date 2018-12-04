<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\UserTagGateway;
use Pina\Modules\Auth\Auth;

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

    public function withDiscount(&$needGroupBy)
    {
        $userId = Auth::userId();
        $discountIds = DiscountGateway::instance()
            ->innerJoin(
                UserTagGateway::instance()->on('tag_id', 'user_tag_id')->onBy('user_id', $userId)
            )
            ->whereBy('enabled', 'Y')
            ->column('id');

        $discountIds = array_merge($discountIds, DiscountGateway::instance()
                ->whereBy('user_tag_id', 0)
                ->whereBy('enabled', 'Y')
                ->column('id'));

        if (empty($discountIds)) {
            return $this;
        }

        $needGroupBy = true;

        return $this
                ->leftJoin(
                    ResourceTreeGatewayExtension::instance()->alias('discount_tree')->on('resource_id', 'id')
                )
                ->leftJoin(
                    ResourceTagGateway::instance()->alias('discount_tags')->on('resource_id', 'id')
                )
                ->leftJoin(
                    DiscountGateway::instance()
                    ->on('parent_id', 'discount_tree.resource_parent_id')
                    //->on('resource_tag_id', 'discount_tags.tag_id')
                    ->onRaw('resource_tag_id = 0 OR resource_tag_id = discount_tags.tag_id')
                    ->onBy('id', $discountIds)
                    ->calculate('IFNULL(MAX(percent), 0) as discount_percent')
        );
    }

    public function whereInStock()
    {
        return $this->innerJoin(
                //ResourceAmountGateway::instance()->on('resource_id', 'id')->whereInStock()
                ResourceInStockGateway::instance()->on('resource_id', 'id')
        );
    }

    public function setListView(&$needGroupBy)
    {
        return $this->select('id')
                ->select('title')
                ->withUrl()
                ->withListTags()
                ->withPriceIfExists()
                ->withDiscount($needGroupBy)
                ->withImage()
                ->leftJoin(
                    ResourceAmountGateway::instance()->on('resource_id', 'id')->select('amount')
        );
    }

}
