<?php

namespace Pina\Modules\PayPal;

use Pina\ModuleInterface;
use Pina\CSRF;

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
        return 'PayPal';
    }

    public function http()
    {
        return CSRF::whitelist([
            'paypal',
            'paypal-check-order',
        ]);
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
