<?php

function smarty_modifier_format_price($price)
{
	return \Pina\Modules\Cart\Price::format($price);
}