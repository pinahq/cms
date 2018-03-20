<?php

use Pina\Modules\CMS\Time;

function smarty_modifier_format_time($date)
{
	return Time::format($date);
}
