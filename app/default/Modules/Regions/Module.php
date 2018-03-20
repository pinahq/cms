<?php

namespace Pina\Modules\Regions;

use Pina\ModuleInterface;

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
        return 'Regions';
    }

    public function http()
    {
        return [
            'countries',
            'regions',
            'cities',
        ];
    }
    
    public function cli()
    {
        
    }
    
    public function boot()
    {
        
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
