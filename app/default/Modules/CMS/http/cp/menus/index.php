<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/menus');

return ['menus' => MenuGateway::instance()->get()];
