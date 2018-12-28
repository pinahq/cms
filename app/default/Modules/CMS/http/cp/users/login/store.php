<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Auth\Auth;

$userId = Request::input('pid');

Auth::loginUsingId($userId);

return Response::ok()->contentLocation('/');