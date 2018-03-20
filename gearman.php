<?php

namespace Pina;

use Pina\Gearman\GearmanEventWorker;

include __DIR__."/bootstrap/autoload.php";

ModuleRegistry::init();
ModuleRegistry::initModules();

$worker = new GearmanEventWorker();

while ($worker->work());
