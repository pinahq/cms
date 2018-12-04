<?php

namespace Pina\Modules\Cart;

class Discount
{

    public static function applyList($resources, $discountField = 'percent')
    {
        foreach ($resources as $k => $resource) {
            if (!isset($resource[$discountField])) {
                continue;
            }
            $resources[$k]['actual_price'] = static::apply($resource['price'], $resource['actual_price'], $resource[$discountField]);
        }

        return $resources;
    }

    public static function apply($price, $actualPrice, $discountPercent)
    {
        if (empty($discountPercent)) {
            return $actualPrice;
        }
        
        $discountedPrice = round($price * (100 - $discountPercent) / 100, 2);
        if ($discountedPrice < $actualPrice) {
            return $discountedPrice;
        }
        
        return $actualPrice;
    }

}
