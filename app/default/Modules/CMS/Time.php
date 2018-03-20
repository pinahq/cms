<?php

namespace Pina\Modules\CMS;

use Pina\Arr;

class Time
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

        $f = "H:i";
        if (!empty($config['format_time'])) {
            $f = $config['format_time'];
        }

        return date($f, $date);
    }
}
