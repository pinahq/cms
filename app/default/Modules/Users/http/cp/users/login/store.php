<?php

namespace Pina\Modules\Users;

use Pina\Request;
use Pina\Response;

$userId = Request::input('pid');

Auth::loginUsingId($userId);

return Response::ok()->contentLocation('/');