<?php

use Pina\Modules\CMS\Date;

function smarty_modifier_format_date($date)
{
    return Date::format($date);
}
