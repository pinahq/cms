<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\DB;

Request::match('cp/:cp/modules/:id');

App::forceMimeType('application/json');

$id = Request::input('id');

$namespace = ModuleGateway::instance()->whereId($id)->value('namespace');

if ($namespace === __NAMESPACE__) {
    return Response::badRequest(__('Operation is not permitted'));
}

if (empty($namespace)) {
    return Response::badRequest(__('Can`t remove the module'));
}

$className = $namespace . '\\Module';
if (!class_exists($className)) {
    ModuleGateway::instance()->whereBy('namespace', $namespace)->delete();
    return Response::ok();
}
$module = new $className;
if (!($module instanceof \Pina\ModuleInterface)) {
    return Response::badRequest(__('Wrong type of the module'));
}
App::container()->get(\Pina\ModuleRegistryInterface::class)->remove($module);

$className = $namespace . '\\Installation';
if (class_exists($className)) {
    $installator = new $className;
    if (!($installator instanceof \Pina\InstallationInterface)) {
        return Response::badRequest(__('Wrong type of the installator'));
    }
    $installator->remove();
}

$upgrades = App::getUpgrades();

$db = App::container()->get(\Pina\DatabaseDriverInterface::class);
$db->batch($upgrades);

return Response::ok();
