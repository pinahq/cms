<?php

namespace Pina\Modules\reCAPTCHA;

use Pina\ModuleInterface;
use Pina\Composer;

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
        return 'reCAPTCHA';
    }

    public function http()
    {
        \Pina\App::container()->share('captcha', \Pina\Modules\reCAPTCHA\Captcha::class);
        Composer::placeView('captcha', 'recaptcha');
        return [
            'recaptcha'
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
