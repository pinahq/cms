<?php

namespace Pina\Modules\Comments;

use Pina\Request;
use Pina\Modules\Auth\Auth;

Request::match('resources/:resource_id/comments');

$resourceId = Request::input('resource_id');

$data = Request::intersect('resource_id', 'text');
$data['user_id'] = Auth::userId();

CommentGateway::instance()->insert($data);