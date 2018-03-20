<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\Config;
use Pina\Arr;

class Price
{
    public static function format($price)
    {
        $config = Config::getNamespace(__NAMESPACE__);

        $defaultDecPoint = '.';
        $defaultThousandSep = ' ';

        $requireFields = ['format_price', 'hide_zeros', 'prefix_currency_symbol', 'suffix_currency_symbol'];
        if (array_diff($requireFields, array_keys($config))) {
            return number_format($price, 2, $defaultDecPoint, $defaultThousandSep);
        }

        list($decPoint, $thousandsSep) = explode('|', $config['format_price']);
        $decPoint = empty($decPoint) ? $defaultDecPoint : $decPoint;
        $thousandsSep = empty($thousandsSep) ? $defaultThousandSep : $thousandsSep;
        $decimals = 2;
        if ($config['hide_zeros'] == 'Y') {
            $decimals = 0;
        }
        
        $price = number_format($price, $decimals, $decPoint, $thousandsSep);
        $price = $config['prefix_currency_symbol'] . $price . $config['suffix_currency_symbol'];

        return $price;
    }
}
