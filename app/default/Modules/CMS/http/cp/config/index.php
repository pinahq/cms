<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\ModuleRegistry;

if (in_array(Request::input('display'), ['menu', 'sidebar'])) {
    $modules = ConfigGateway::instance()
        ->select('namespace')
        ->groupBy('namespace')
        ->orderBy('namespace')
        ->get();
    foreach ($modules as $k => $module) {
        $namespace = $module['namespace'];
        $m = ModuleRegistry::get($namespace);
        if (empty($m)) {
            unset($modules[$k]);
            continue;
        }
        $modules[$k]['module_title'] = $m->getTitle();
    }
    return ['modules' => $modules];
}
