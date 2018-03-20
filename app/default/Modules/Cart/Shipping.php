<?php

namespace Pina\Modules\Cart;

class Shipping
{

    public static function getMethods($countryKey, $regionKey, $cityId, $cascade = true)
    {
        $sms = ShippingMethodGateway::instance()
            ->whereBy('enabled', 'Y')
            ->select('id')
            ->select('title')
            ->select('description')
            ->orderBy('order', 'asc')
            ->get();

        foreach ($sms as $k => $v) {
            $fee = Shipping::fee($v['id'], $countryKey, $regionKey, $cityId, $cascade);
            if ($fee === false) {
                unset($sms[$k]);
                continue;
            }
            $sms[$k]['fee'] = $fee;
        }
        
        return $sms;
    }

    public static function fee($shippingMethodId, $countryKey, $regionKey, $cityId, $cascade = true)
    {
        $fee = ShippingFeeGateway::instance()
            ->whereBy('shipping_method_id', $shippingMethodId)
            ->whereBy('country_key', $countryKey)
            ->whereBy('region_key', $regionKey)
            ->whereBy('city_id', $cityId)
            ->value('fee');

        if ($fee === false && $cascade) {
            $fee = ShippingFeeGateway::instance()
                ->whereBy('shipping_method_id', $shippingMethodId)
                ->whereBy('country_key', $countryKey)
                ->whereBy('region_key', $regionKey)
                ->whereBy('city_id', 0)
                ->value('fee');
        }

        if ($fee === false && $cascade) {
            $fee = ShippingFeeGateway::instance()
                ->whereBy('shipping_method_id', $shippingMethodId)
                ->whereBy('country_key', $countryKey)
                ->whereBy('region_key', '')
                ->whereBy('city_id', 0)
                ->value('fee');
        }

        return $fee;
    }

}
