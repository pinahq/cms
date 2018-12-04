<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Modules\Auth\Auth;

return ['isLogged' => Auth::check()];
