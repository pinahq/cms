<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\Modules\Users\Auth;
use Pina\Event;

Request::match('submissions');

$params = Request::all();
unset($params['post']);
unset($params['__resource']);
unset($params['__module']);

foreach ($params as $key => $value) {
    $params[$key] = strip_tags($value);
}

$schema = [
    'type',
    'subject',
    'email',
    'firstname',
    'lastname',
    'middlename',
    'phone',
];

$data = [];
foreach ($schema as $item) {
    if (isset($params[$item])) {
        $data[$item] = $params[$item];
        unset($params[$item]);
        continue;
    }
    
    $key = substr($item, strlen('submission') + 1);
    if (isset($params[$key])) {
        $data[$item] = $params[$key];
        unset($params[$key]);
        continue;
    }
}

if (!empty($params['resource_id'])) {
    $data['resource_id'] = intval($params['resource_id']);
} else {
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $parsed = parse_url($_SERVER['HTTP_REFERER']);
        $resource = !empty($parsed['path'])?trim($parsed['path'], '/'):'';
        if (!empty($resource)) {
            $resourceId = ResourceUrlGateway::instance()
                ->whereBy('url', $resource)
                ->innerJoin(
                    ResourceTypeGateway::instance()->on('id', 'resource_type_id')
                )
                ->value('resource_id');
            
            $data['resource_id'] = intval($resourceId);
        }
    }
}

$data['data'] = !empty($params)?json_encode($params, JSON_UNESCAPED_UNICODE):'';

$data['user_id'] = Auth::userId();

$submissionId = SubmissionGateway::instance()->insertGetId($data);

Event::trigger('submission.created', $submissionId);

return Response::ok()->json([]);