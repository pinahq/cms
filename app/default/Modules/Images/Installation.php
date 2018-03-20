<?php

namespace Pina\Modules\Images;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());
        
        if (empty($moduleId)) {
            throw new \Exception('can not install module '.__NAMESPACE__);
            return;
        }

    }

    public static function remove()
    {
        #echo 'remove';
    }

}
