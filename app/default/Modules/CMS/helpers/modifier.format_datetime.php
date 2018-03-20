<?php

use Pina\Modules\CMS\Date;
use Pina\Modules\CMS\Time;

function smarty_modifier_format_datetime($date)
{
    return Date::format($date) .' '. Time::format($date);
}
