<?php

namespace Pina\Modules\CMS;

use Pina\Arr;

class Date
{
    public static function format($date)
    {
        if (empty($date) || $date == '0000-00-00 00:00:00' || $date == "0000-00-00") {
            return '-';
        }

        $config = Config::getNamespace(__NAMESPACE__);

        if (is_string($date)) {
            $date = strtotime($date);
        }

        $f = "d.m.Y";
        if (!empty($config['format_date'])) {
            $f = $config['format_date'];
        }

        return date($f, $date);
    }
}
