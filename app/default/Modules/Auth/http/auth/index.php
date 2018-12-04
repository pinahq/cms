<?php

namespace Pina\Modules\Auth;

Auth::init();

return ['user' => Auth::user()];
