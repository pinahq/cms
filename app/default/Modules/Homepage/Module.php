<?php

namespace Pina\Modules\Homepage;

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
        return 'Homepage';
    }

    public function http()
    {
        return [
            '/',
            '/errors',
            '/favicon',
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
