<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\ModuleRegistryInterface;

Request::match('cp/:cp/modules');

$systemNamespaces = \Pina\Config::get('modules');

$modules = ModuleGateway::instance()->orderBy('namespace', 'asc')->get();
$installedNamespaces = [];

foreach ($modules as $k => $module) {
    $installedNamespaces[] = $module['namespace'];
    
    $modules[$k]['installed'] = 'Y';
    if (in_array($module['namespace'], $systemNamespaces)) {
        $modules[$k]['system'] = 'Y';
    }
    
    $className = $module['namespace'] . '\\Module';
    if (!class_exists($className)) {
        $modules[$k]['removed'] = 'Y';
        continue;
    }
    $m = new $className;
    $modules[$k]['title'] = $m->getTitle();
}


$path = App::path()."/default/Modules";
$newModules = [];
if (file_exists($path)) {
    $directories = array_diff(scandir($path), ['.', '..']);
    $existedNamespaces = array_map(function($a) {
        return 'Pina\\Modules\\'.$a;
    }, $directories);
    $newModules = array_diff($existedNamespaces, $installedNamespaces);
}

foreach ($newModules as $k => $namespace) {
    $className = $namespace. '\\Module';
    if (!class_exists($className)) {
        unset($newModules[$k]);
        continue;
    }
    $m = new $className;
    $newModules[$k] = [
        'namespace' => $namespace,
        'title' => $m->getTitle(),
        'installed' => 'N',
        'system' => in_array($namespace, $systemNamespaces) ? 'Y' : 'N',
    ];
}

$modules = array_merge($modules, $newModules);

usort($modules, function($a, $b) {
    return $a['title'] >= $b['title'];
});

return [
    'modules' => $modules,
];
