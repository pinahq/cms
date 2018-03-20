<?php

namespace Pina\Modules\CMS;

use Pina\Request;

Request::match('cp/:cp/resources/:id/row');

return Request::input('resource');