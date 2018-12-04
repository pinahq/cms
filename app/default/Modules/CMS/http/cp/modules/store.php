<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\DB;

Request::match('cp/:cp/modules');

App::forceMimeType('application/json');

$namespace = Request::input('namespace');
if (empty($namespace)) {
    return Response::badRequest(__('Please specify namespace'), 'namespace');
}

$className = $namespace . '\\Module';
if (!class_exists($className)) {
    return Response::badRequest(__('Can`t install the module'));
}
$module = new $className;
if (!($module instanceof \Pina\ModuleInterface)) {
    return Response::badRequest(__('Wrong type of the module'));
}

App::container()->get(\Pina\ModuleRegistryInterface::class)->add($module);

$upgrades = App::getUpgrades();

$db = DB::get();
$db->batch($upgrades);

$className = $namespace . '\\Installation';
if (class_exists($className)) {
    $installator = new $className;
    if (!($installator instanceof \Pina\InstallationInterface)) {
        return Response::badRequest(__('Wrong type of the installator'));
    }
    $installator->install();
}

return Response::ok();
