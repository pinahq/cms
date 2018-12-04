<?php

namespace Pina\Modules\CMS;

use Pina\Modules\Auth\Auth;

Auth::init();

return ['user' => Auth::user()];
