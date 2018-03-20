<?php

namespace Pina\Modules\MailChimp;

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
        return 'MailChimp';
    }
    
    public function http()
    {
        return [];
    }
    
    public function cli()
    {
        return [
            'mailchimp',
        ];
    }
    
    public function boot()
    {
        Event::subscribe($this, 'user.subscribed', 'mailchimp.subscribe');
        Event::subscribe($this, 'user.unsubscribed', 'mailchimp.unsubscribe');

    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
