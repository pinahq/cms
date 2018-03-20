<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Arr;

Request::match('cp/:cp/config/:namespace');

$params = ConfigGateway::instance()
    ->whereNamespace(Request::input('namespace'))
    ->orderBy('order', 'ASC')
    ->get();
$groupParams = Arr::group($params, 'group');
foreach ($groupParams as $group => $params) {
    foreach ($params as $k => $param) {
        if (!empty($param['variants'])) {
            $params[$k]['variants'] = json_decode($param['variants'], true);
        }
    }
    $groupParams[$group] = $params;
}

return ['group_params' => $groupParams];
