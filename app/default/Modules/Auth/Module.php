<?php

namespace Pina\Modules\Auth;

use Pina\ModuleInterface;

use Pina\Route;
use Pina\Access;
use Pina\Composer;
use Pina\Event;
use Pina\App;

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
        return 'Auth';
    }

    public function http()
    {

        return [
            '/auth',

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
