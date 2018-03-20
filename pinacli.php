<?php

namespace Pina;

include __DIR__."/vendor/autoload.php";

App::init('cli', __DIR__.'/config');
CLI::handle($argv, basename(__FILE__));