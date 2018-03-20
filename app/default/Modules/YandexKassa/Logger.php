<?php

namespace Pina\Modules\YandexKassa;

use Pina\Log;

Class Logger
{
	public function log($mes)
	{
		Log::warning('yandex-kassa', $mes);
	}
}
