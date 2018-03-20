<?php

namespace Pina\Modules\RetailCRM;

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
        return 'RetailCRM';
    }
    
    public function http()
    {
        return [
            'retailcrm'
        ];
    }
    
    public function cli()
    {
        
    }
    
    public function boot()
    {
        Event::subscribe($this, 'order.placed', 'retailcrm.sync');
        Event::subscribe($this, 'order.updated', 'retailcrm.sync');
        Event::subscribe($this, 'retailcrm.update');
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}