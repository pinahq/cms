<?php

namespace Pina;

#ini_set('display_errors', 'on');
#error_reporting(E_ALL);

include "../bootstrap/autoload.php";

App::run();

#echo memory_get_peak_usage();