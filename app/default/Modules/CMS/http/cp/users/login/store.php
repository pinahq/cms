<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

$userId = Request::input('pid');

Auth::loginUsingId($userId);

return Response::ok()->contentLocation('/');