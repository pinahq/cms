<?php

namespace Pina\Modules\Import;

use Pina\ModuleInterface;
use Pina\Event;

class Module implements ModuleInterface
{

    public function getPath()
    {
        return __DIR__;
    }
    
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'Import';
    }
    
    public function frontend()
    {
        return [];
    }
    
    public function cli()
    {
        return [];
    }
    
    public function boot()
    {
        
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}