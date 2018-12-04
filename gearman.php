<?php

namespace Pina;

use Pina\Gearman\GearmanEventWorker;

include __DIR__."/bootstrap/autoload.php";

$modules = App::container()->get(ModuleRegistryInterface::class);
$modules->boot('gearman');

$worker = new GearmanEventWorker();

while ($worker->work());
