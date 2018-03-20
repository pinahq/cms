<?php

namespace Pina;

use Pina\Modules\Users\Auth;

Auth::init();

return ['user' => Auth::user()];
