<?php

namespace Pina\Modules\Media;

use Pina\ModuleInterface;
use Pina\DispatcherRegistry;

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
        return 'Media';
    }

    public function http()
    {
        DispatcherRegistry::register(new Dispatcher());
        
        return [
            'resize',
        ];
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
