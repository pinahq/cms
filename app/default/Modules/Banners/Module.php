<?php

namespace Pina\Modules\Banners;

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
        return 'Banners';
    }
    
    public function http()
    {
        return ['banner-content', 'cp/banner-content'];
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