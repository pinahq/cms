<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;

$configNamespace = Request::input('namespace');
if (empty($configNamespace)) {
    return Response::badRequest(__('Введите идентификатор модуля'), 'namespace');
}

$params = Request::input('params');
if (!empty($params) && is_array($params)) {
    $update = [];
    foreach($params as $name => $value) {
        $update[] = [
            'namespace' => $configNamespace,
            'key' => $name,
            'value' => $value
        ];
    }
    
    ConfigGateway::instance()->put($update);
}

return Response::ok();