<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Log;
use Pina\CLI;
use Pina\Modules\Import\OfferImport;

CLI::arguments()->add([
    'import_id' => [
        'required' => true,
        'castTo' => 'int',
    ],
]);

try {
    CLI::arguments()->parse();
} catch (\Exception $e) {
    CLI::usage();
    return;
}

$importId = CLI::arguments()->get('import_id');

try {
    $import = new OfferImport($importId);
    
    $import->import();
} catch (\Exception $e) {
    Log::error('ModuleImport.GermanTasks.start', $e->getMessage());
}