<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\DB;

Request::match('cp/:cp/modules/:id/status');

$id = Request::input('id');

$ns = ModuleGateway::instance()->whereId($id)->value('namespace');

$moduleRegistry = App::container()->get(\Pina\ModuleRegistryInterface::class);

if (Request::input('enabled') == 'Y') {
    $moduleRegistry->turnOn($ns);
} else {
    $moduleRegistry->turnOff($ns);
}

$upgrades = App::getUpgrades();

$db = DB::get();
$db->batch($upgrades);

return Response::ok()->json(['enabled' => Request::input('enabled')]);
